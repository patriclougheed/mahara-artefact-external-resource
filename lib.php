<?php

/**
 *
 * @package    mahara
 * @subpackage artefact-extresource
 * @author     laurent.opprecht@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 University of Geneva http://www.unige.ch/
 *
 */
defined('INTERNAL') || die();


require_once dirname(__FILE__) . '/lib/renderer/asset_renderer.class.php';

class PluginArtefactExtresource extends PluginArtefact
{

    public static function get_headers()
    {
        $headers = array();
//        $javascript = get_config('wwwroot') . 'artefact/feedback/js/feedback.js';
//        $javascript = '<script type="text/javascript" src="' . $javascript . '"></script>';
//        $headers[] = $javascript;
//
//        $style = get_config('wwwroot') . 'artefact/ple/theme/raw/static/style/style.css';
//        $style = '<link rel="stylesheet" type="text/css" href="' . $style . '">';
//        $headers[] = $style;
        return $headers;
    }

    public static function get_artefact_types()
    {
        return array(
            'extresource',
        );
    }

    public static function get_block_types()
    {
        return array();
    }

    public static function get_plugin_name()
    {
        return 'extresource';
    }

    public static function menu_items()
    {
        return array();
    }

    public static function get_event_subscriptions()
    {
        return array();
    }

    public static function get_activity_types()
    {
        return array();
    }

    public static function postinst($prevversion)
    {
        return true;
    }

    public static function view_export_extra_artefacts($viewids)
    {
        $artefacts = array();
        //@TODO:
        return $artefacts;
    }

    public static function artefact_export_extra_artefacts($artefactids)
    {
        $artefacts = array();
        //@TODO:
        return $artefacts;
    }

}

class ArtefactTypeExtresource extends ArtefactType
{

    const TABLE_NAME = 'artefact_extresource';

//    const DEFAULT_LIMIT = 3;

    /**
     * The hash function used to compute the url hash.
     * 
     * Note that urls can be longuer than 255 chars and that such strings will not
     * be indexed in the DB. 
     * 
     * To circuvent this issue we compute a hash of the url and store in the 
     * table with the url. The hash field is indexed to ensure fast artefact 
     * retrieval from the url.
     * 
     * @param string $url URL to hash
     * @return string The hash
     */
    public static function hash($url)
    {
        return md5($url);
    }

    public static function filter_url($url)
    {
        $url = trim($url);
        if (strpos($url, 'http') !== 0)
        {
            $url = trim($url, '/');
            $url = 'http://' . $url;
        }

        $result = filter_var($url, FILTER_VALIDATE_URL);
        return $result;
    }

    public static function is_url($url)
    {
        return filter_url($url) != false;
    }

    /**
     * Create an artefact from the url. 
     * 
     * If an artefact already exists for this url it is retrieved from the DB
     * and it values are updated from the one computed from the url. In this
     * case the change is not commited to the DB. 
     * 
     * @param string $url
     * @return ArtefactTypeExtresource|null
     */
    public static function create($url)
    {
        $asset = AssetRenderer::get($url);
        if (empty($asset))
        {
            return null;
        }
        $data = array();
        $data['title'] = isset($asset[AssetRenderer::TITLE]) ? $asset[AssetRenderer::TITLE] : '';
        $data['description'] = isset($asset[AssetRenderer::DESCRIPTION]) ? $asset[AssetRenderer::DESCRIPTION] : '';
        $data['ref'] = isset($asset[AssetRenderer::URL]) ? $asset[AssetRenderer::URL] : $url;
        $data['hash'] = self::hash($data['url']);
        $data['snippet'] = isset($asset[AssetRenderer::EMBED_SNIPET]) ? $asset[AssetRenderer::EMBED_SNIPET] : '';
        $data['thumbnail'] = isset($asset[AssetRenderer::THUMBNAIL]) ? $asset[AssetRenderer::THUMBNAIL] : '';
        $data['metadata'] = serialize($asset);
        $data['source'] = '';
        $data['kind'] = 'html';


        $result = new self(0, $data);

        global $view;
        if (!empty($view))
        {
            if ($group_id = $view->get('group'))
            {
                $result->set('group', $group_id);
            }
        }
        return $result;
    }

//    public static function get_group_artefacts($group_id)
//    {
//        if (empty($group_id))
//        {
//            return array();
//        }
//        $sql = 'SELECT a.*, e.* FROM artefact as a, ' . self::TABLE_NAME . ' as e where a.id = e.artefact AND a.group = ' . $group_id . ' ORDER BY a.id DESC';
//        return get_records_sql_array($sql, $values = array());
//    }
//
//    public static function get_user_artefacts($user_id)
//    {
//        if (empty($user_id))
//        {
//            return array();
//        }
//        $sql = 'SELECT a.*, e.* FROM artefact as a, ' . self::TABLE_NAME . ' as e where a.id = e.artefact AND a.group IS NULL AND a.owner = ' . $user_id . ' ORDER BY a.id DESC';
//        return get_records_sql_array($sql, $values = array());
//    }

    /**
     * 
     * @param string $id
     * @return ArtefactTypeExtresource|null
     */
    public static function select_by_id($id)
    {
        if (empty($id))
        {
            return null;
        }
        $data = get_record('artefact', 'id', $id);
        return $data ? new self($id, $data) : null;

//        if (empty($artefactid))
//        {
//            return null;
//        }
//        if (!is_array($artefactid))
//        {
//            $artefactid = array($artefactid);
//        }
//
//        $idstr = join(',', array_map('intval', $artefactids));
//        $where = 'artefact IN (' . $idstr . ')';
//
//        $sql = 'SELECT
//                    a.title, a.description, a.author, a.authorname, a.ctime, 
//                    r.*
//                FROM {artefact} a
//                    INNER JOIN {' . self::TABLE_NAME . '} r ON a.id = r.artefact 
//                WHERE ' . $where . '
//                ORDER BY a.ctime';
//        $data = get_records_sql_array($sql, array());
//        $data = count($artefactid) == 1 ? reset($data) : $data;
//        return new self($result);
    }

    /**
     *
     * @param string $url
     * @return ArtefactTypeExtresource|null
     */
    public static function select_by_url($url)
    {
        if (empty($url))
        {
            return null;
        }
        $hash = self::hash($url);
        $url = strtolower($url);

        $sql = 'SELECT
                    a.title, a.description, a.author, a.authorname, a.ctime, 
                    r.*
                FROM {artefact} a
                    INNER JOIN {' . self::TABLE_NAME . '} r ON a.id = r.artefact 
                WHERE hash="' . $hash . '"';
        $records = get_records_sql_array($sql, array());
        $records = $records ? array() : $records;
        foreach ($records as $record)
        {
            if (strtolower($record['url']) == $url)
            {
                return new self($record['id'], $record);
            }
        }
        return null;
    }

//    public static function get_limit($value = null)
//    {
//        $value = (int) $value;
//        $result = $value ? $value : self::DEFAULT_LIMIT;
//        //@todo: fetch the value from configuration
//        return $result;
//    }
//    public static function get_id($object)
//    {
//        return is_object($object) && !is_null($object) ? $object->get('id') : (int) $object;
//    }
//    public static function get_view_id($object = null)
//    {
//        if (is_null($object))
//        {
//            global $view;
//            return self::get_id($view);
//        }
//        else
//        {
//            return self::get_id($object);
//        }
//    }

    public static function is_active()
    {
        return get_field('artefact_installed', 'active', 'name', 'resource');
    }

//    private static $allow_copy = true;
//
//    /**
//     * If true then resources can be copied. 
//     *
//     * @return bool
//     */
//    public static function allow_copy()
//    {
//        return self::$allow_copy;
//    }
//
//    public static function allow_copy_on()
//    {
//        self::$allow_copy = true;
//    }
//
//    public static function allow_copy_off()
//    {
//        self::$allow_copy = false;
//    }
//    private static $allow_notification = true;
//
//    /**
//     * If true then notification is allowed. If false not.
//     *
//     * @return bool
//     */
//    public static function allow_notification()
//    {
//        return self::$allow_notification;
//    }
//
//    public static function allow_notification_on()
//    {
//        self::$allow_notification = true;
//    }
//
//    public static function allow_notification_off()
//    {
//        self::$allow_notification = false;
//    }

    /**
     * Returns true if the logged in user is the owner of $view_id. Returns false otherwise.
     *
     * @global User $USER
     * @global View $view
     * @param int $view_id
     * @return bool
     */
//    public static function is_view_owner($view_id = null)
//    {
//        global $USER;
//        global $view;
//
//        $the_view = is_null($view_id) ? $view : new View($view_id);
//        $view_owner_id = $the_view ? $the_view->get('owner') : false;
//        $user_id = self::get_id($USER);
//        $result = $user_id != 0 && $user_id == $view_owner_id;
//        return $result;
//    }

    protected $ref;
    protected $hash;
    protected $kind;
    protected $metadata;
    protected $snippet;
    protected $thumbnail;
    protected $source;

    public function __construct($id = 0, $data = null)
    {
        parent::__construct($id, $data);

        if ($this->id && ($extra = get_record(self::TABLE_NAME, 'artefact', $this->id)))
        {
            foreach ($extra as $name => $value)
            {
                if (property_exists($this, $name))
                {
                    $this->{$name} = $value;
                }
            }
        }
    }

    public function commit()
    {
        global $USER;
        if (empty($this->dirty))
        {
            return;
        }

        $new = empty($this->id);


        if (!$this->get('owner'))
        {
            $this->set('owner', $USER->get('id'));
        }

        db_begin();
        parent::commit();

        $data = (object) array(
                    'artefact' => $this->get('id'),
                    'ref' => $this->get('ref'),
                    'kind' => $this->get('kind'),
                    'metadata' => $this->get('metadata'),
                    'snippet' => $this->get('snippet'),
                    'thumbnail' => $this->get('thumbnail'),
                    'source' => $this->get('source'),
        );

        if ($new)
        {
            insert_record(self::TABLE_NAME, $data);
        }
        else
        {
            update_record(self::TABLE_NAME, $data, 'artefact');
        }
        db_commit();
        $this->dirty = false;

//        if (self::allow_notification())
//        {
//            $message = new StdClass;
//            $message->users = array($owner);
//            $message->subject = get_string('subject', 'artefact.resource', '');
//            $message->message = get_string('message', 'artefact.resource', '');
//
//            require_once('activity.php');
//            activity_occurred('maharamessage', $message);
//        }
    }

    public static function is_singular()
    {
        return false;
    }

    public static function get_icon($options = null)
    {
        global $THEME;
        return $THEME->get_url('images/resource.gif', false, 'artefact/resource');
    }

    public function delete()
    {
        if (empty($this->id))
        {
            return;
        }

        db_begin();
        //$this->detach();
        delete_records(self::TABLE_NAME, 'artefact', $this->id);
        parent::delete();
        db_commit();

//        if (self::allow_notification())
//        {
//            $message = new StdClass;
//            $message->users = array($owner);
//            $message->subject = get_string('subject', 'artefact.resource', '');
//            $message->message = get_string('message', 'artefact.resource', '');
//
//            require_once('activity.php');
//            activity_occurred('maharamessage', $message);
//        }
    }

    public static function bulk_delete($artefactids)
    {
        if (empty($artefactids))
        {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select(self::TABLE_NAME, 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }

    public static function get_links($id)
    {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/ple/view.php?id=' . $id,
        );
    }

    public function can_have_attachments()
    {
        return false;
    }

    public function render_self()
    {
        return array('html' => $this->get('snippet'));
    }

    public function exportable()
    {
        return true;
    }

}