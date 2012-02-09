<?php

/**
 * Google document renderer. 
 * 
 * @see http://support.google.com/docs/bin/answer.py?hl=en&answer=86101&topic=1360911&ctx=topic
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht
 */
class AssetGoogleDocumentRenderer extends AssetRenderer
{

    /**
     *
     * @param HttpResource $asset 
     */
    public function accept($asset)
    {       
        $url = $asset->url();
        
        return strpos($url, 'docs.google.com/document/pub') !== false;
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
        <iframe width="100%" height="350" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0" src="$url"></iframe>
EOT;


        $result = array();
        $result[self::EMBED_SNIPET] = $embed;
        $result[self::TITLE] = $title;
        $result[self::DESCRIPTION] = $description;
        $result[self::TAGS] = $keywords;
        return $result;
    }

}