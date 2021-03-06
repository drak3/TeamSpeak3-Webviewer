<?php

/**
 *  This file is part of devMX TeamSpeak3 Webviewer.
 *  Copyright (C) 2011 - 2012 Max Rath and Maximilian Narr
 *
 *  devMX TeamSpeak3 Webviewer is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  TeamSpeak3 Webviewer is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with devMX TeamSpeak3 Webviewer.  If not, see <http://www.gnu.org/licenses/>.
 */
class style extends ms_Module
{

    public $styles_sent;
    protected $styles = array();

    public function init()
    {
        $this->styles_sent = false;
    }

    function onStartup()
    {

        $filepath = s_root . 'styles/' . $this->config['style'] . '/' . $this->config['style'] . '.css';

        if (!file_exists($filepath)) die('style_not_found');

        $style = $this->config['style'];

        $this->config['style'] = $this->appendVersionString(s_http . 'styles/' . $this->config['style'] . '/' . $this->config['style'] . '.css');
        $this->config['style_ie'] = $this->appendVersionString(s_http . 'styles/' . $style . '/' . $style . '_ie.css');
        $this->styles[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $this->config['style'] . "\" >\r\n";
        $this->styles[] = '<!--[if IE]><link rel="stylesheet" type="text/css" href="' . $this->config['style_ie'] . '"><![endif]-->';
    }

    public function loadStyle($text, $type = 'file', $cc = NULL)
    {
        if (!$this->styles_sent)
        {
            $style = "";
            switch ($type)
            {
                case 'file':
                    $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $this->appendVersionString($text) . "\" >\r\n";
                    break;
                case 'cc':
                    $style = '<!--[if ' . $cc . ']><style type="text/css">' . $text . '</style><![endif]-->';
                    break;
                default:
                    $style = "<style type=\"text/css\">
				<!--
				$text
				-->
				</style>";
                    break;
            }

            if (!in_array($style, $this->styles)) $this->styles[] = $style;
        }
    }

    public function onHtmlStartup()
    {
        if (!$this->mManager->moduleIsLoaded('htmlframe') && !$this->styles_sent)
        {
            $this->styles_sent = true;
            return implode("", $this->styles);
        }
    }

    public function onHead()
    {
        if (!$this->styles_sent)
        {
            $this->styles_sent = true;
            return implode("", $this->styles);
        }
    }
 
    /**
     * Appends a version string to a url
     * @param string $text
     * @return string
     * @since 1.4
     * @author Maximilian Narr
     */
    private function appendVersionString($text)
    {
        return $text . sprintf("?v=%s", version);
    }

}
