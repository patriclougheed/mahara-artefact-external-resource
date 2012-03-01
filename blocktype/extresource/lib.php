<?php

/**
 *
 * @package    mahara
 * @subpackage artefact-extresource-extresource
 * @author     laurent.opprecht@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 University of Geneva http://www.unige.ch/
 *
 */
defined('INTERNAL') || die();

safe_require('artefact', 'extresource');

class PluginBlocktypeExtresource extends PluginBlocktype
{
    
    public static $default_size = 250;

    public static function get_string($identifier, $section = 'blocktype.extresource/extresource')
    {
        return get_string($identifier, $section);
    }

    public static function get_title()
    {
        return self::get_string('title');
    }

    public static function get_description()
    {
        return self::get_string('description');
    }

    public static function get_categories()
    {
        return array('external');
    }

    /**
     * Optional method. If exists, allows this class to decide the title for 
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $instance)
    {
        $configdata = $instance->get('configdata');

        $title = $instance->get('title');
        if ($title)
        {
            return $title;
        }

        $artefact_id = isset($configdata['artefactid']) ? $configdata['artefactid'] : 0;
        $artefact = ArtefactTypeExtresource::select_by_id($artefact_id);
        return $artefact ? $artefact->get('title') : '';
    }

    /**
     * Allows block types to override the instance's title.
     *
     * For example: My Views, My Groups, My Friends, Wall
     */
    public static function override_instance_title(BlockInstance $instance)
    {
        $configdata = $instance->get('configdata');
        $title = $instance->get('title');
        if ($title)
        {
            return $title;
        }

        $artefact_id = isset($configdata['artefactid']) ? $configdata['artefactid'] : 0;
        $artefact = ArtefactTypeExtresource::select_by_id($artefact_id);
        return $artefact ? $artefact->get('title') : '';
    }

    public static function render_instance(BlockInstance $instance, $editing = false)
    {
        $configdata = $instance->get('configdata');
        $artefact_id = isset($configdata['artefactid']) ? $configdata['artefactid'] : 0;
        $artefact = ArtefactTypeExtresource::select_by_id($artefact_id);
        $snippet = $artefact ? $artefact->get('snippet') : '';

        $smarty = smarty_core();
        $smarty->assign('snippet', $snippet);
        return $smarty->fetch('blocktype:extresource:content.tpl');
    }

    public static function has_instance_config()
    {
        return true;
    }

    public static function instance_config_form($instance)
    {
        /**
         * Note that the default behaviour of contextualHelp does not allow to
         * retrieve a help page for an block contained in an artefact. So we 
         * have to do it differently.
         */
        global $THEME;
        $img = $THEME->get_url('images/icon_help.png');        
        $urlhelp =<<<EOT
        <a onclick="contextualHelp('instconf','url','artefact','extresource','','',this); return false;" href="">
            <img title="Help" alt="Help" src="$img">
        </a>
EOT;
        
        $configdata = $instance->get('configdata');
        return array(
            'url' => array(
                'type' => 'text',
                'title' => self::get_string('url'),
                'size' => 100,
                'defaultvalue' => isset($configdata['url']) ? $configdata['url'] : null,
                'rules' => array(
                    'required' => true
                )
            ),
            'urlhelp' => array(
                'type' => 'html',
                'value' => $urlhelp,
                    
            ),
            'artefactid' => array(
                'type' => 'hidden',
                'value' => isset($configdata['artefactid']) ? $configdata['artefactid'] : null
            ),
            'size' => array(
                'type' => 'text',
                'title' => self::get_string('size'),
                'size' => 3,
                'rules' => array(
                    'required' => false,
                    'integer' => true,
                    'minvalue' => 100,
                    'maxvalue' => 800,
                ),
                'description' => self::get_string('size_description'),
                'defaultvalue' => (!empty($configdata['size'])) ? $configdata['size'] : self::$default_size,
            ),
        );
    }

    public static function artefactchooser_element($default = null)
    {
        
    }

    public static function instance_config_save($values)
    {
        $values['url'] = ArtefactTypeExtresource::filter_url($values['url']);
        $url = $values['url'];
        $id = $values['artefactid'];

        if ($id)
        {
            $artefact = ArtefactTypeExtresource::select_by_id($id);
            $artefact->delete();
            $values['artefactid'] = 0;
        }

        if (empty($url))
        {
            return $values;
        }
        
        $config = array();
        $config['size'] = isset($values['size']) ? $values['size'] : self::$default_size;
        $artefact = ArtefactTypeExtresource::create($url, $config);

        global $view;
        if ($group_id = $view->get('group'))
        {
            $artefact->set('group', $group_id);
        }

        $artefact->commit();

        $id = $artefact->get('id');
        $id = $id ? $id : 0;
        $values['artefactid'] = $id;

        return $values;
    }

    public static function default_copy_type()
    {
        return 'shallow';
    }

    public static function allowed_in_view(View $view)
    {
        return true;
    }

    /**
     * subclasses can override this if they need to do something a bit special
     * eg more than just what the BlockInstance->delete function does.
     * 
     * @param BlockInstance $instance
     */
    public static function delete_instance(BlockInstance $instance)
    {
        $configdata = $instance->get('configdata');
        $id = isset($configdata['artefactid']) ? $configdata['artefactid'] : null;
        if ($id)
        {
            $artefact = ArtefactTypeExtresource::select_by_id($id);
            $artefact->delete();
        }
    }

}
