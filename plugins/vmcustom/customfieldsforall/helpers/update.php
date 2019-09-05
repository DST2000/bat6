<?php
/**
 *
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2014-2017 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class CustomFieldsForAllUpdate
{

    /**
     *
     * @var string
     */
    protected $type = 'plugin';

    /**
     *
     * @var string
     */
    protected $extension='customfieldsforall';

    /**
     * The extension name as used in the update table
     *
     * @var string
     */
    protected $name='Custom Fields For All';


    /**
     * The id of the update stream
     *
     * @var int
     */
    protected $streamId = 6;


    /**
     * Get the params
     *
     * @return JRegistry
     */
    protected function getParams()
    {
        if($this->params ===  null) {
            $plugin = JPluginHelper::getPlugin('vmcustom', $this->extension);
            $this->params = new JRegistry($plugin->params);
        }

        return $this->params;
    }

    /**
     * Get the update id from the updates table
     *
     * @param string $extension
     * @param string $type
     * @since 2.1.0
     */
    public function getExtensionId(){
        // Get the extension ID to ourselves
        $db=JFactory::getDbo();
        $query = $db->getQuery(true)
        ->select($db->quoteName('extension_id'))
        ->from($db->quoteName('#__extensions'))
        ->where($db->quoteName('type') . ' = ' . $db->quote($this->type))
        ->where($db->quoteName('element') . ' = ' . $db->quote($this->extension));
        $db->setQuery($query);

        $extension_id = $db->loadResult();

        if (empty($extension_id))
        {
            return;
        }
        return $extension_id;
    }

    /**
     * Refreshes the Joomla! update sites for this extension as needed
     *
     * @return  void
     */
    public function refreshUpdateSite()
    {
        $app = JFactory::getApplication();
        $params = $this->getParams();
        $dlid = $params->get('update_dlid', '');
        $extra_query = null;

        // If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
        if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
        {
            $extra_query = 'dlid=' . $dlid;
        }

        // Create the update site definition we want to store to the database
        $update_site = array(
            'name'		=> $this->name,
            'type'		=> 'extension',
            'location' => 'https://breakdesigns.net/index.php?option=com_ars&view=update&task=stream&format=xml&id='.(int)$this->streamId,
            'enabled'	=> 1,
            'last_check_timestamp'	=> 0,
            'extra_query'	=> $extra_query
        );

        $extension_id=$this->getExtensionId();

        if (empty($extension_id))
        {
            return;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
        ->select($db->quoteName('update_site_id'))
        ->from($db->quoteName('#__update_sites_extensions'))
        ->where($db->quoteName('extension_id') . ' = ' . $db->quote($extension_id));
        $db->setQuery($query);

        $updateSiteIDs = $db->loadColumn(0);

        // No update sites defined. Create a new one.
        if (!count($updateSiteIDs))
        {
            $newSite = (object)$update_site;
            $db->insertObject('#__update_sites', $newSite);
            $id = $db->insertid();

            $updateSiteExtension = (object)array(
                'update_site_id'	=> $id,
                'extension_id'		=> $extension_id,
            );
            $db->insertObject('#__update_sites_extensions', $updateSiteExtension);
        }
        else
        {
            // Loop through all update sites
            foreach ($updateSiteIDs as $id)
            {
                $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__update_sites'))
                ->where($db->quoteName('update_site_id') . ' = ' . $db->quote($id));
                $db->setQuery($query);
                $aSite = $db->loadObject();

                // Does the name and location match?
                if (($aSite->name == $update_site['name']) && ($aSite->location == $update_site['location']))
                {
                    // Do we have the extra_query property (J 3.2+) and does it match?
                    if (property_exists($aSite, 'extra_query'))
                    {
                        if ($aSite->extra_query == $update_site['extra_query'])
                        {
                            continue;
                        }
                    }
                    else
                    {
                        // Joomla! 3.1 or earlier. Updates may or may not work.
                        continue;
                    }
                }

                $update_site['update_site_id'] = $id;
                $newSite = (object)$update_site;
                $db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
            }
        }
    }
}
