<?php
/**
 * Video-Form v0.0.1
 *
 * This plugin adds an Youtube video form field to the content tab.
 *
 * Licensed under the MIT license, see LICENSE.
 *
 * @package     Video-Form
 * @version     0.0.1
 * @link        <https://github.com/whollaus/Grav-Plugin-Video-Form>
 * @author      Wolfgang Hollaus <wh@hollaus-it.at>
 * @copyright   2018, Wolfgang Hollaus
 * @license     <http://opensource.org/licenses/MIT> MIT
 */

namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Data\Blueprints;
use RocketTheme\Toolbox\Event\Event;


/**
 *  Video Form Plugin
 *
 * This plugin adds an Youtube video form field to the content tab.
 */
class VideoFormPlugin extends Plugin
{

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {

        if ($this->isAdmin()) {
            $this->active = false;
            $events = [
                'onBlueprintCreated' => ['onBlueprintCreated', 0]
            ];
            $this->enable($events);
        }

    }

    /**
     * Extend Content blueprints with the Video Form
     *
     * @param Event $event
     */
    public function onBlueprintCreated(Event $event)
    {
        $newtype = $event['type'];

        $in_templates = $this->config->get('plugins.video-form.templates');

        if (in_array($newtype, $in_templates)) {

            $blueprint = $event['blueprint'];
            if ($blueprint->get('form/fields/tabs', null, '/')) {

                if (!empty($this->config->get('plugins.video-form.yt_enabled'))) {
                    $blueprints = new Blueprints(__DIR__ . '/blueprints/');
                    $extends = $blueprints->get('youtube');
                    $blueprint->extend($extends, true);
                }

                if (!empty($this->config->get('plugins.video-form.uv_enabled'))) {
                    $blueprints = new Blueprints(__DIR__ . '/blueprints/');
                    $extends = $blueprints->get('upload');
                    $blueprint->extend($extends, true);
                }

            }
        }

    }

    /**
     * Add Frontend Output
     */
    public function onPageInitialized()
    {

        $page = $this->grav['page'];

        $in_templates = $this->config->get('plugins.video-form.templates');

        $newtype = $page->template();

        // Youtube Video
        if (!empty($this->config->get('plugins.video-form.yt_enabled')) && !empty($in_templates) && !empty($newtype) && in_array($newtype, $in_templates) && isset($page->header()->yt_video)) {
            $this->youtube($page);
        }

        // Upload Video
        if (!empty($this->config->get('plugins.video-form.uv_enabled')) && !empty($in_templates) && !empty($newtype) && in_array($newtype, $in_templates) && isset($page->header()->uv_video)) {
            $this->html5_video_player($page);
        }

    }

    /**
     * html5 Video Player
     */
    private function html5_video_player($page)
    {

        sort($page->header()->uv_video);

        if (!empty($page->header()->uv_video[0]) && !empty($page->header()->uv_video[0]['path'])) {

            $parameters = ' type="video/mp4" codeload="auto"';

            if (empty($this->config->get('plugins.video-form.uv_autoplay'))) {

            } else {
                $parameters .= ' autoplay';
            }

            if (empty($this->config->get('plugins.video-form.uv_controls'))) {

            } else {
                $parameters .= ' controls';
            }

            if (empty($this->config->get('plugins.video-form.uv_loop'))) {

            } else {
                $parameters .= ' loop';
            }

            if (empty($this->config->get('plugins.video-form.uv_muted'))) {

            } else {
                $parameters .= ' muted';
            }

            if (empty($this->config->get('plugins.video-form.uv_playsinline'))) {

            } else {
                $parameters .= ' playsinline';
            }


            if (empty($page->header()->uv_image)) {

            } else {
                sort($page->header()->uv_image);
                if (!empty($page->header()->uv_image[0]['path'])) {
                    $parameters .= ' poster="' . $page->header()->uv_image[0]['path'] . '"';
                }
            }

            $video_content = '<div class="video-form">';
            $video_content .= '<div class="video-form-uv">';
            $video_content .= '<video src="'.$page->header()->uv_video[0]['path'].'" '.$parameters.'></video>';
            $video_content .= '</div>';
            $video_content .= '</div>';

            $page->setRawContent($page->content() . $video_content);

            $this->grav['assets']->add('plugin://video-form/css/video-form.css');


        }

    }

    /**
     * Youtube Player
     */
    private function youtube($page)
    {

        $video_arr = parse_url($page->header()->yt_video);

        $video = '';
        if (!empty($video_arr['host']) && $video_arr['host'] == 'youtu.be' && !empty($video_arr['path'])) {
            $video = str_replace('/', '', $video_arr['path']);
        } elseif (!empty($video_arr['query'])) {
            parse_str($video_arr['query'], $query);
            if (!empty($query['v'])) {
                $video = $query['v'];
            }
        }

        if (!empty($video)) {

            $parameters = array();

            if (empty($this->config->get('plugins.video-form.yt_autoplay'))) {
                $parameters['autoplay'] = 0;
            } else {
                $parameters['autoplay'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_cc_load_policy'))) {
                $parameters['cc_load_policy'] = 0;
            } else {
                $parameters['cc_load_policy'] = 1;
            }

            if (!empty($this->config->get('plugins.video-form.yt_color')) && $this->config->get('plugins.video-form.yt_color') == 'red') {
                $parameters['color'] = 'red';
            } elseif (!empty($this->config->get('plugins.video-form.yt_color')) && $this->config->get('plugins.video-form.yt_color') == 'white') {
                $parameters['color'] = 'white';
            }

            if (empty($this->config->get('plugins.video-form.yt_controls'))) {
                $parameters['controls'] = 0;
            } else {
                $parameters['controls'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_disablekb'))) {
                $parameters['disablekb'] = 0;
            } else {
                $parameters['disablekb'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_enablejsapi'))) {
                $parameters['enablejsapi'] = 0;
            } else {
                $parameters['enablejsapi'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_fs'))) {
                $parameters['fs'] = 0;
            } else {
                $parameters['fs'] = 1;
            }

            if (!empty($this->config->get('plugins.video-form.yt_hl'))) {
                $parameters['hl'] = $this->config->get('plugins.video-form.yt_hl');
            }

            if (empty($this->config->get('plugins.video-form.yt_iv_load_policy'))) {
                $parameters['iv_load_policy'] = 0;
            } else {
                $parameters['iv_load_policy'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_loop'))) {
                $parameters['loop'] = 0;
            } else {
                $parameters['loop'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_modestbranding'))) {
                $parameters['modestbranding'] = 0;
            } else {
                $parameters['modestbranding'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_origin'))) {
                $parameters['origin'] = '';
            } else {
                if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                    $parameters['origin'] = 'https://' . $_SERVER['REMOTE_ADDR'];
                } else {
                    $parameters['origin'] = 'http://' . $_SERVER['REMOTE_ADDR'];
                }
            }

            if (empty($this->config->get('plugins.video-form.yt_rel'))) {
                $parameters['rel'] = 0;
            } else {
                $parameters['rel'] = 1;
            }

            if (empty($this->config->get('plugins.video-form.yt_showinfo'))) {
                $parameters['showinfo'] = 0;
            } else {
                $parameters['showinfo'] = 1;
            }

            $video_content = '<div class="video-form">';
            $video_content .= '<div class="video-form-yt">';
            $video_content .= '<iframe src="https://www.youtube.com/embed/' . $video . '?' . http_build_query($parameters) . "\n" . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
            $video_content .= '</div>';
            $video_content .= '</div>';

            $page->setRawContent($page->content() . $video_content);

            $this->grav['assets']->add('plugin://video-form/css/video-form.css');

        }

    }

}