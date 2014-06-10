<?php

namespace Modera\TranslationsBundle\TokenExtraction;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Attempts to extract translation tokens from PHP classes. This is what you should do to let this extractor
 * detect and extract tokens from your PHP classes:
 *
 * 1) Import T class from Helper package, for example:
 *    use Modera\FoundationBundle\Translation\T;
 *
 * Do not use aliases! For example, this won't be detected ( yet ):
 *    use Modera\FoundationBundle\Translation\T as Translator;
 * 2) In your code use method T::trans() or T::transChoice. Samples:
 *
 *    By using string literals:
 *      T::trans('Hello')
 *      T::trans('Hello, %name%', array('name' => 'Bob'))
 *      T::trans('Achtung!', null, 'errors')
 *
 *    If you have a long message then you can use this syntax:
 *      $message = 'Achtung! ';
 *      $message.= 'Dear %name%, ';
 *      $message.= "you don't have anough privileges to perform this action!";
 *
 *      T::trans($mesage, array('name' => 'Bob'));
 *
 *    When this code is parsed you will get this translation token:
 *    Achtung! Dear %name%, you don't have anough privileges to perform this action!
 *
 *    Please keep in mind, that you can't when perform any manipulations or string that are going to be
 *    part of token, for example, you can't do this:
 *      $message = ucfirst('Achtung!');
 *    When tokens are being extracted from code it is being statically analyzed when when functions are invoked
 *    their values will be resolved during execution phase.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PhpClassTokenExtractor implements ExtractorInterface
{
    /**
     * Prefix for new found message.
     *
     * @var string
     */
    private $prefix = '';

    /**
     * {@inheritDoc}
     */
    public function extract($directory, MessageCatalogue $catalog)
    {
        // load any existing translation files
        $finder = new Finder();
        $files = $finder->files()->name('*.php')->exclude('Tests')->in($directory);
        foreach ($files as $file) {
            $this->parseTokens(token_get_all(file_get_contents($file)), $catalog);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Normalizes a token.
     *
     * @param mixed $token
     * @return string
     */
    protected function normalizeToken($token)
    {
        if (is_array($token)) {
            return $token[1];
        }

        return $token;
    }

    /**
     * @param array $tokens
     * @return array[]
     */
    private function extractInvocations(array $tokens)
    {
        $sequences = [
            ['T', '::', 'trans'],
            ['T', '::', 'transChoice']
        ];

        $invocations = array();

        foreach ($sequences as $seq) {
            foreach ($tokens as $tokenIndex=>$token) {
                $matchCount = 0;
                foreach ($seq as $seqIndex=>$item) {
                    $indexToValidate = $tokenIndex + $seqIndex; // next token in a token stream

                    if (isset($tokens[$indexToValidate]) && $this->normalizeToken($tokens[$indexToValidate]) == $item) {
                        $matchCount++;
                    }
                }

                // we will continue only if we got exact match for entire sequence of tokens
                if ($matchCount != count($seq)) {
                    continue;
                }

                $startIndex = $tokenIndex + count($seq);

                if ($tokens[$startIndex] != '(') {
                    continue;
                }

                $startIndex++;
                $depth = 1; // because there was already one "("
                $bodyLength = null;

                $bodyStartTokens = array_slice($tokens, $startIndex);
                foreach ($bodyStartTokens as $braceWannaBeIndex=>$braceWannaBeToken) {
                    $value = $this->normalizeToken($braceWannaBeToken);

                    if ('(' == $value) {
                        $depth++;
                    } else if (')' == $value) {
                        $depth--;
                    }

                    if (0 == $depth) {
                        $bodyLength = $braceWannaBeIndex;
                        break;
                    }
                }

                $bodyTokens = array_slice($tokens, $startIndex, $bodyLength);

                $invocations[] = array(
                    'method_name' => $seq[count($seq)-1],
                    'start_index' => $startIndex,
                    'body' => $bodyTokens,
                    'length' => $bodyLength,
                    'tokens' => $tokens
                );
            }
        }

        return $invocations;
    }

    /**
     * Will filter out whitespace tokens because we don't use them in during tokens stream analysis
     *
     * @param array $tokens
     * @return array[]
     */
    private function siftOutWhitespaceTokens(array $tokens)
    {
        $result = [];

        foreach ($tokens as $token) {
            if (is_array($token) && \T_WHITESPACE == $token[0]) {
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }

    /**
     * @param array $invocation
     * @return array[]
     */
    private function extractArgumentTokens(array $invocation)
    {
        $tokens = $invocation['body'];

        // if method contains no parameters, like Helper::trans()
        if (count($tokens) == 0) {
            return array();
        }

        $args = array(
            'token' => $tokens[0],
            'params' => array(),
            'domain' => array()
        );

        // both trans, transChoice first argument is "message":
        // trans($id, $parameters, $domain, $locale)
        // transChoice($id, $number, $parameters, $domain, $locale)
        // In case of "transChoice" we assume that parameter "$number" is represented by one token followed
        // by "," -- this gives us index shift of 2
        $indexShift = 'transChoice' == $invocation['method_name'] ? 2 : 0;

        $isParamsArgSpecified = isset($tokens[$indexShift+1]) && ',' == $tokens[$indexShift+1]
                             && isset($tokens[$indexShift+2]);

        if ($isParamsArgSpecified) {
            $isArrayParameter = strtolower($this->normalizeToken($tokens[$indexShift+2])) == 'array'
                              && isset($tokens[$indexShift+3]) && $this->normalizeToken($tokens[$indexShift+3]) == '(';

            $isNullParameter = strtolower($this->normalizeToken($tokens[$indexShift+2])) == 'null';

            if ($isArrayParameter) {
                $depth = 1;
                $secondArgEndIndex = null;

                for ($i=$indexShift+4; $i<count($tokens); $i++) {
                    $value = $this->normalizeToken($tokens[$i]);

                    // parameters may be nested
                    if ('(' == $value) {
                        $depth++;
                    } else if (')' == $value) {
                        $depth--;
                    }

                    if (0 == $depth) {
                        $secondArgEndIndex = $i;
                        break;
                    }
                }

                // token parameters
                $secondArgumentTokens = array_slice($tokens, $indexShift+4, $secondArgEndIndex);
                $args['params'] = $secondArgumentTokens;

                // if $params argument is followed by "," we assume that domain is specified
                $isDomainArgumentSpecified = isset($tokens[$indexShift+$secondArgEndIndex+1])
                                           && ',' == $tokens[$indexShift+$secondArgEndIndex+1]
                                           && isset($tokens[$indexShift+$secondArgEndIndex+2]);

                if ($isDomainArgumentSpecified) {
                    $args['domain'] = $tokens[$indexShift+$secondArgEndIndex+2];
                }
            } else if ($isNullParameter) { // second parameter is
                $args['params'] = $tokens[$indexShift+2];

                // if params are followed by "null," we assume that domain parameter is also provided
                $isDomainArgumentSpecified = isset($tokens[$indexShift+3])
                                          && $this->normalizeToken($tokens[$indexShift+3]) == ','
                                          && isset($tokens[$indexShift+4]);
                
                if ($isDomainArgumentSpecified) {
                    $args['domain'] = $tokens[$indexShift+4];
                }
            }
        }

        return $args;
    }

    /**
     * @param array $valueToken
     * @param array $invocation
     *
     * @return string
     */
    private function resolveTokenValue(array $valueToken, array $invocation)
    {
        if (\T_CONSTANT_ENCAPSED_STRING == $valueToken[0]) {
            // just a string literal

            return trim($valueToken[1], $valueToken[1]{0});
        } else if (\T_VARIABLE == $valueToken[0]) {
            // variable is used, we are going to try to resolve its value even if it is composite
            // ( made up of several assign statements )

            $variableName = $valueToken[1];

            // narrowing variable value assign. zone
            $parentTokens = array_slice($invocation['tokens'], 0, $invocation['start_index']);
            $parentTokens = array_reverse($parentTokens);

            $length = null;
            foreach ($parentTokens as $i=>$parentToken) {
                if (is_array($parentToken) && \T_FUNCTION == $parentToken[0]) {
                    $length = $i;
                    break;
                }
            }

            $parentTokens = array_slice($parentTokens, 0, $length);
            $parentTokens = array_reverse($parentTokens);

            // now that we have all tokens from FUNCTION to the Helper::*() we can compile variable's value

            $variableValue = '';
            foreach ($parentTokens as $i=>$parentToken) {
                // ha, this is our variable!
                if (is_array($parentToken) && \T_VARIABLE == $parentToken[0] && $parentToken[1] == $variableName) {
                    // both assign operator and a value exist
                    if (isset($parentTokens[$i+1]) && isset($parentTokens[$i+2])) {
                        $assignValueTokenValue = $parentTokens[$i+1];
                        $variableValueTokenValue = $parentTokens[$i+2];

                        $isValidAssignToken = false;
                        if (is_string($assignValueTokenValue) && '=' == $assignValueTokenValue) {
                            $isValidAssignToken = true;
                        } else if (is_array($assignValueTokenValue) && \T_CONCAT_EQUAL == $assignValueTokenValue[0]) {
                            $isValidAssignToken = true;
                        }

                        // we are not going to support assign statement when one variable points to another etc
                        $isValidVarValueToken = is_array($variableValueTokenValue) && \T_CONSTANT_ENCAPSED_STRING == $variableValueTokenValue[0];

                        if ($isValidAssignToken && $isValidVarValueToken) {
                            $value = $this->normalizeToken($variableValueTokenValue);
                            $assignStmt = $this->normalizeToken($assignValueTokenValue);

                            if ('=' == $assignStmt) {
                                $variableValue = trim($value, $value{0});
                            } else if ('.=' == $assignStmt) {
                                $variableValue .= trim($value, $value{0});
                            }
                        }
                    }
                }
            }

            return $variableValue;
        } else {
            return 'Error! Token value can be either a literal string or variable reference.';
        }
    }

    /**
     * Will make sure if a token stream which represents a file has required USE statement
     *
     * @param array $tokens
     * @return bool
     */
    private function containsRequiredUseStatements(array $tokens)
    {
        foreach ($tokens as $currentIndex=>$token) {
            if (!is_array($token)) {
                continue;
            }

            if (\T_USE === $token[0]) {
                $expectedSequence = ['Modera', '\\', 'FoundationBundle', '\\', 'Translation', '\\', 'T'];

                $currentSequence = array_slice($tokens, $currentIndex + 1, count($expectedSequence));

                if (implode('', $expectedSequence) == $this->joinTokenSequence($currentSequence)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function joinTokenSequence(array $tokenSequence)
    {
        $result = [];

        foreach ($tokenSequence as $token) {
            $result[] = is_array($token) ? $token[1] : $token;
        }

        return implode('', $result);
    }

    private function parseTokens(array $tokens, MessageCatalogue $catalog)
    {
        $tokens = $this->siftOutWhitespaceTokens($tokens);

        if (!$this->containsRequiredUseStatements($tokens)) {
            return false;
        }

        $invocations = $this->extractInvocations($tokens);

        foreach ($invocations as $invocation) {
            $argumentsTokens = $this->extractArgumentTokens($invocation);
            if (count($argumentsTokens) == 0) {
                continue;
            }

            $tokenValue = $this->resolveTokenValue($argumentsTokens['token'], $invocation);
            $domain = count($argumentsTokens['domain']) > 0
                    ? $this->resolveTokenValue($argumentsTokens['domain'], $invocation)
                    : 'messages';

            $catalog->set($this->prefix . $tokenValue, $tokenValue, $domain);
        }
    }
}