<?php
/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $strip_teaser Optional. Strip teaser content before the more text. Default is false.
 */
function the_content_filtered($more_link_text = null, $strip_teaser = false)
{
    $content = get_the_content($more_link_text, $strip_teaser);

    $content = save_modules('price-calculator', $content);
    $content = save_modules('reservation-plan', $content);
    $content = save_modules('gallery', $content);

    /**
     * Filters the post content.
     *
     * @since 0.71
     *
     * @param string $content Content of the current post.
     */
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);

    $content = replace_template('price-calculator', $content);
    $content = replace_template('reservation-plan', $content);
    $content = replace_template('gallery', $content);

    echo $content;
}

function save_modules($tag, $content) {
    return preg_replace("/\[\s*$tag(.*?)\]/", "<$tag\$1></$tag>", $content);
}

class TemplateException extends Exception { }

$ml_regexAttrName = '[a-zA-Z][\w\-]*[\w\d]';
$ml_regexAttrValue = '[\w\-,]*';
$ml_regexAttr = "($ml_regexAttrName)\s*=\s*\"($ml_regexAttrValue)\"";
$ml_regexAttrs = "$ml_regexAttr(\s+$ml_regexAttr)*?";

function ml_regexFrom($tag)
{
    global $ml_regexAttrs;
    return "/<$tag(\s($ml_regexAttrs)\s*|\s*)(>(.*)<\/$tag>|\/>)/";
    // return "/&lt;$tag(\s($ml_regexAttrs)\s*|\s*)(&gt;(.*)&lt;\/$tag&gt;|\/&gt;)/";
    // return "/\[\s*$tag(\s($ml_regexAttrs)\s*|\s*)\]/";
}

function parameterMap($key, $values)
{
    $data = array();
    for ($i = 0; $i < count($key); ++$i) {
        $data[$key[$i]] = $values[$i];
    }
    return $data;
}

function replace_template($xmlTag, $content)
{
    global $ml_regexAttr;

    if (preg_match_all(ml_regexFrom($xmlTag), $content, $matches)) {
        for ($i = 0; $i < count($matches[0]); ++$i) {
            $replace = $matches[0][$i];
            $attributes = $matches[1][$i];
            $body = $matches[9][$i];
            preg_match_all("/$ml_regexAttr/", $attributes, $matches);
            ob_start();
            $parameters = parameterMap($matches[1], $matches[2]);
            include(get_template_directory() . "/module-templates/$xmlTag.module.php");
            $templateContent = ob_get_clean();
            $templateContent = trim(preg_replace('/\s+/', ' ', $templateContent));
            $content = str_replace($replace, $templateContent, $content);
        }
    }
    return $content;
}

function the_exerpt_filtered()
{
    $content = get_the_excerpt();
    $content = preg_replace(ml_regexFrom('price-calculator'), '<p><a href="preise#price_calc">&raquo; Preisrechner</a></p>', $content);
    $content = preg_replace(ml_regexFrom('reservation-plan'), '<p><a href="#">&raquo; Reservierungsplan</a></p>', $content);

    $content = apply_filters('the_excerpt', $content);
    echo $content;
}

?>