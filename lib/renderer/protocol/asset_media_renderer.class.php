<?php

/**
 * Media renderer. I.e. video streams that can be embeded through an embed tag.
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht
 */
class AssetMediaRenderer extends AssetRenderer
{

    /**
     *
     * @param HttpResource $asset 
     */
    public function accept($asset)
    {
        if ($asset->is_video())
        {
            return true;
        }
        
        //swf mime type is application/x-shockwave-flash
        return $asset->has_ext('swf');
    }

    /**
     *
     * @param HttpResource $asset 
     */
    public function render($asset)
    {
        if (!$this->accept($asset))
        {
            return;
        }

        $url = $asset->url();

        $title = $asset->title();
        $description = $asset->get_meta('description');
        $keywords = $asset->get_meta('keywords');

        $embed = <<<EOT
        <embed width="100%" name="plugin" src="$url" >
EOT;


        $result = array();
        $result[self::EMBED_SNIPET] = $embed;
        $result[self::TITLE] = $title;
        $result[self::DESCRIPTION] = $description;
        $result[self::TAGS] = $keywords;
        return $result;
    }

}