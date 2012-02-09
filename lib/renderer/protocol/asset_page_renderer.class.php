<?php

/**
 * Generic HTML page renderer. Process any html page.
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht
 */
class AssetPageRenderer extends AssetRenderer
{

    /**
     *
     * @param HttpResource $asset 
     */
    public function render($asset)
    {
        global $THEME;
        $url = $asset->url();
        $title = $asset->title();
        $title = $title ? $title : $asset->name();
        $description = $asset->get_meta('description');
        $description = $description;

        $keywords = $asset->get_meta('keywords');

        $image_src = $asset->get_link('rel', 'image_src');
        $image_src = $image_src ? $image_src['href'] : false;

        if (empty($image_src))
        {
            $image_src = $asset->get_link('rel', 'apple-touch-icon');
            $image_src = $image_src ? $image_src['href'] : false;
        }
        if (empty($image_src))
        {
            $image_src = $asset->get_link('rel', 'fluid-icon');
            $image_src = $image_src ? $image_src['href'] : false;
        }
        if (empty($image_src))
        {
            $image_src = $asset->get_link('rel', 'shortcut icon');
            $image_src = $image_src ? $image_src['href'] : false;
        }
        if (empty($image_src))
        {
            $image_src = $asset->get_link('rel', 'icon');
            $image_src = $image_src ? $image_src['href'] : false;
        }
        if (empty($image_src))
        {
            $image_src = $THEME->get_url('images/internet.png', false, 'artefact/extresource');
        }
        
        if (strpos($image_src, '//') === 0)
        {
            $image_src = "http:$image_src";
        }
        else if (strpos($image_src, '/') === 0) //relative url to the root 
        {
            $url = $asset->url();
            $protocol = reset(explode('://', $url));
            $domain = end(explode('://', $url));
            $domain = reset(explode('/', $domain));
            $image_src = "$protocol://$domain/$image_src";
        }
        else if (strpos($image_src, 'http') !== 0) //relative url to the document
        {
            $url = $asset->url();
            $tail = end(explode('/', $url));
            $base = str_replace($tail, '', $url);

            $image_src = $base . $image_src;
        }

        $embed = <<<EOT
        <a href="$url">
            <img src="{$image_src}" alt="{$title}" title="{$title}" style="float:left; margin-right:5px; margin-bottom:5px; " >
        </a>
        $description
        <span style="clear:both;"></span>
EOT;


        $result = array();
        $result[self::EMBED_SNIPET] = $embed;
        $result[self::TITLE] = $title;
        $result[self::THUMBNAIL] = $image_src;
        $result[self::DESCRIPTION] = $description;
        $result[self::TAGS] = $keywords;
        return $result;
    }

}