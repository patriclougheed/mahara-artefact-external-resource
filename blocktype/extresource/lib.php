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

        $artefact_id = $configdata['artefactid'];
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
        
        $artefact_id = $configdata['artefactid'];
        $artefact = ArtefactTypeExtresource::select_by_id($artefact_id);
        return $artefact ? $artefact->get('title') : '';
    }

    public static function render_instance(BlockInstance $instance, $editing = false)
    {
        $configdata = $instance->get('configdata');
        $artefact_id = $configdata['artefactid'];
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
        $configdata = $instance->get('configdata');
        return array(
            'url' => array(
                'type' => 'text',
                'title' => self::get_string('url'),
                'description' => self::get_string('url_description'),
                'size' => 100,
                'defaultvalue' => isset($configdata['url']) ? $configdata['url'] : null,
                'rules' => array(
                    'required' => true
                ),
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

        if ($url)
        {
            $artefact = ArtefactTypeExtresource::create($url);

            global $view;
            if ($group_id = $view->get('group'))
            {
                $artefact->set('group', $group_id);
            }

            $artefact->commit();
            $id = $artefact->get('id');
            $id = $id ? $id : 0;
            $values['artefactid'] = $id;
        }
        else
        {
            $values['artefactid'] = 0;
        }
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

}
