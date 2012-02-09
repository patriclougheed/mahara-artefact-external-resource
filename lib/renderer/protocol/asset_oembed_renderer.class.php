<?php

/**
 * Process html pages that support the oembed protocol.
 * 
 * @see http://oembed.com/
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht
 * 
 */
class AssetOembedRenderer extends AssetRenderer
{

    /**
     *
     * @param HttpResource $asset 
     */
    public function render($asset)
    {
        $link = $asset->get_link('type', 'application/json+oembed');
        if (empty($link))
        {
            return false;
        }

        $href = $link['href'];
        $data = HttpResource::fetch_json($href . '&maxwidth=300');
        if (empty($data))
        {
            return false;
        }

        $data['title'] = isset($data['title']) ? $data['title'] : '';
        $data['width'] = isset($data['width']) ? intval($data['width']) : '';
        $data['height'] = isset($data['height']) ? intval($data['height']) : '';
        
        $type = $data['type'];
        $f = array($this, "render_$type");
        if (is_callable($f))
        {
            $result = call_user_func($f, $asset, $data);
        }
        else
        {
            $result = array();
        }
        $result[self::THUMBNAIL] = isset($data['thumbnail_url']) ? $data['thumbnail_url'] : '';
        $result[self::TITLE] = isset($data['title']) ? $data['title'] : '' ;

        return $result;
    }

    protected function render_photo($asset, $data)
    {
        if ($data['type'] != 'photo')
        {
            return array();
        }
        $result = array();

        $title = $data['title'];
        $width = $data['width'];
        $height = $data['height'];
        $ratio = $height / $width;
        $base = min(300, $width);
        $width = $base;
        $height = $ratio * $base;

        $url = $data['url'];

        $embed = <<<EOT
        <a href="$url"><img src="{$url}" width="{$width}" height="{$height}" "alt="{$title}" title="{$title}"></a>
EOT;

        $result[self::EMBED_SNIPET] = '<div style="text-align:center"><div style="display:inline-block">' . $data['html'] .'</div></div>';
        return $result;
    }

    protected function render_video($asset, $data)
    {
        if ($data['type'] != 'video')
        {
            return array();
        }
        $result = array();
        $result[self::EMBED_SNIPET] = '<div style="text-align:center"><div style="display:inline-block">' . $data['html'] .'</div></div>';
        return $result;
    }

    protected function render_rich($asset, $data)
    {
        if ($data['type'] != 'rich')
        {
            return array();
        }

        $result = array();
        $result[self::EMBED_SNIPET] = '<div style="text-align:center"><div style="display:inline-block">' . $data['html'] .'</div></div>';
        return $result;
    }

    protected function render_link($asset, $data)
    {
        if ($data['type'] != 'link')
        {
            return array();
        }
        return array();
    }

}