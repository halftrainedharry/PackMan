<?php
namespace PackMan\Processors\Profile;

use MODX\Revolution\Processors\Processor;
use PackMan\Model\pacProfile;

/**
 * Get a profile
 *
 * @package packman
 * @subpackage processors
 */
class Get extends Processor
{
    public function process()
    {
        if (empty($this->properties['id'])) return $this->failure($this->modx->lexicon('packman.profile_err_ns'));
        $profile = $this->modx->getObject(pacProfile::class, $this->properties['id']);
        if (empty($profile)) return $this->failure($this->modx->lexicon('packman.profile_err_nf'));

        $profileArray = $profile->toArray();

        /* reformat data to work right */
        $data = $profile->get('data');

        /* templates */
        if (!empty($data['templates'])) {
            $tpls = array();
            foreach ($data['templates'] as $tpl) {
                $tpls[] = array(
                    $tpl['id'],
                    $tpl['name'],
                    $tpl['directory'],
                );
            }
            $profile->set('templates','(' . $this->modx->toJSON($tpls) . ')');
        }

        /* chunks */
        if (!empty($data['chunks'])) {
            $tpls = array();
            foreach ($data['chunks'] as $tpl) {
                $tpls[] = array(
                    $tpl['id'],
                    $tpl['name'],
                );
            }
            $profile->set('chunks','(' . $this->modx->toJSON($tpls) . ')');
        }

        /* snippets */
        if (!empty($data['snippets'])) {
            $tpls = array();
            foreach ($data['snippets'] as $tpl) {
                $tpls[] = array(
                    $tpl['id'],
                    $tpl['name'],
                    $tpl['assets_path'],
                    $tpl['core_path'],
                );
            }
            $profile->set('snippets','(' . $this->modx->toJSON($tpls) . ')');
        }


        /* packages */
        if (!empty($data['packages'])) {
            $tpls = array();
            foreach ($data['packages'] as $tpl) {
                $tpls[] = array(
                    $tpl['signature'],
                );
            }
            $profile->set('packages','(' . $this->modx->toJSON($tpls) . ')');
        }


        /* plugins */
        if (!empty($data['plugins'])) {
            $tpls = array();
            foreach ($data['plugins'] as $tpl) {
                $tpls[] = array(
                    $tpl['id'],
                    $tpl['name'],
                );
            }
            $profile->set('plugins','(' . $this->modx->toJSON($tpls) . ')');
        }

        /* directories */
        if (!empty($data['directories'])) {
            $tpls = array();
            foreach ($data['directories'] as $tpl) {
                $tpls[] = array(
                    $tpl['source'],
                    $tpl['target'],
                );
            }
            $profile->set('directories','(' . $this->modx->toJSON($tpls) . ')');
        }

        return $this->success('', $profile);
    }

    public function getLanguageTopics()
    {
        return ['packman:default'];
    }
}
