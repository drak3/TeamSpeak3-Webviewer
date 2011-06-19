<?php

class channelHiding extends ms_Module
{

    function __construct($info, $config, $lang, $mm)
    {
        parent::__construct($info, $config, $lang, $mm);
        $this->mManager->loadModule('jQueryUI');
        $append_config = isset($_GET['config']) ? " , config: '".$_GET['config']."' " : "";
        $ops = "var channelHiding_ops = {\r\n";
        $managevars = false;
        if ($this->config['remember_hidden_chans'])
        {
            if (isset($_GET['config']))
            {
                if(isset($_SESSION['data_manager'][$_GET['config']]['channelHiding'])) {
                    $managevars = $_SESSION['data_manager'][$_GET['config']]['channelHiding'];
                }
                
            }
            else {
                if(isset($_SESSION['data_manager']['channelHiding'])) {
                    $managevars = $_SESSION['data_manager']['channelHiding'];
                }
            }
            
            if($managevars) {
                foreach ($managevars as $to_hide => $value)
                {
                    if ($to_hide != '')
                    {
                        $ops .= "'" . htmlspecialchars($to_hide, ENT_QUOTES) . "': true,";
                        $hidden[] = $to_hide;
                    }
                }
            }

        }

        if ($this->config['hide_empty_chans'])
        {
            foreach ($this->info['channellist'] as $key => $channel)
            {
                $parent = $channel->getParent();
                if (!empty($hidden) && in_array($parent['channel_name'], $hidden))
                {
                    $hidden[] = $channel['channel_name'];
                }
                if ($channel->isEmpty() and $channel->has_childs() && (!empty($hidden) && !(in_array($channel['channel_name'], $hidden))))
                {
                    $ops .= "'" . htmlspecialchars("channel_".$channel['cid'], ENT_QUOTES) . "': true,";
                    $hidden[] = $channel['channel_name'];
                }
            }
            
            if (!is_numeric($this->config['fadeIn_time']) && !in_array($this->config['fadeIn_time'], Array('slow', 'fast')))
            {
                $this->config['fadeIn_time'] = '400';
            }
            if (!is_numeric($this->config['fadeOut_time']) && !in_array($this->config['fadeOut_time'], Array('slow', 'fast')))
            {
                $this->config['fadeOut_time'] = '400';
            }
            
        }
        if ($this->config['remember_hidden_chans']  || $this->config['hide_empty_chans'])
            {
                $ops = rtrim($ops, ",");
            $ops .= "};\r\n";
                $this->mManager->loadModule('js')->loadJS($ops . "$(document).ready(function() {

										$('.channel').each( function() {

											if(channelHiding_ops[$(this).attr('id')] == true) {

												var ms_chan_con = $(this).children('.chan_content');
												ms_chan_con.siblings().fadeOut(0);
                                                                                                ms_chan_con.children('.arrow').switchClass('arrow-normal', 'arrow-hidden', 500);
												ms_chan_con.attr('is_hidden','true');
											}
										});
										$('.spacer').each( function() {
											if(channelHiding_ops[$(this).attr('id')] == true) {
												var ms_chan_con = $(this).children('.spacer_con');
												ms_chan_con.siblings().fadeOut(0);
                                                                                                ms_chan_con.children('.arrow').switchClass('arrow-normal', 'arrow-hidden', 500);
												ms_chan_con.attr('is_hidden','true');
											}
										});});", 'text');
            }
            
            $this->mManager->loadModule('js')->loadJS("$(document).ready(function() { 
											$('.chan_content').click(function() {
											var ms_id = $(this).parent().attr('id');
											if($(this).attr('is_hidden') == 'true') {
												$(this).siblings().fadeIn(" . $this->config['fadeIn_time'] . ");			
                                                                                                $(this).children('.arrow').switchClass('arrow-hidden', 'arrow-normal', 500);
												$(this).attr('is_hidden','false');
												$.get('" . s_http . "data_manager.php',{action: 'delete', field: 'channelHiding', id: ms_id ".$append_config."});
											}
											else{
												$(this).siblings().fadeOut(" . $this->config['fadeOut_time'] . ");
                                                                                                $(this).children('.arrow').switchClass('arrow-normal', 'arrow-hidden', 500);
												$(this).attr('is_hidden','true');
												$.get('" . s_http . "data_manager.php',{action: 'save', field: 'channelHiding', id: ms_id, data: 'true' ".$append_config."});
											}
										});
										$('.spacer_con').click(function() {
											var ms_id = $(this).parent().attr('id');
											if($(this).attr('is_hidden') == 'true') {
												$(this).siblings().fadeIn(" . $this->config['fadeIn_time'] . ");											
                                                                                                $(this).children('.arrow').switchClass('arrow-hidden', 'arrow-normal', 500);
												$(this).attr('is_hidden','false');
												$.get('" . s_http . "data_manager.php',{action: 'delete', field: 'channelHiding', id: ms_id ".$append_config."});
											}
											else{
												$(this).siblings().fadeOut(" . $this->config['fadeOut_time'] . ");
                                                                                                $(this).children('.arrow').switchClass('arrow-normal', 'arrow-hidden', 500);
												$(this).attr('is_hidden','true');
												$.get('" . s_http . "data_manager.php',{action: 'save', field: 'channelHiding', id: ms_id, data: 'true' ".$append_config."});
											}
										});
});", 'text');
        }

    

}

