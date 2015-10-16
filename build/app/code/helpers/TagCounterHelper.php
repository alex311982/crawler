<?php

/**
 * Tag counter helper
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class TagCounterHelper
{

    /**
     * Count by tags
     *
     * @param array|string $context
     * @param array|string $tags
     *
     * @return array
     */
    static public function count($context, $tags)
    {
        !is_array($context) ? $context = array($context) : '';
        !is_array($tags) ? $tags = array($tags) : '';
        $result = array();

        foreach($tags as $tag) {
            foreach($context as $html) {
                if (empty($html)) {
                    $result[$tag] = 0;
                } else {
                    $dom = new DOMDocument;
                    $dom->loadHTML($html);
                    $allElements = $dom->getElementsByTagName($tag);
                    $result[$tag] = $allElements->length;
                }
            }
        }

        return $result;
    }
}
