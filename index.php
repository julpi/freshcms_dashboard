<?php

/*
 * Dashboard - Frog CMS dashboard plugin
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/dashboard
 *
 */

define('DASHBOARD_LOG_EMERG',    0);
define('DASHBOARD_LOG_ALERT',    1);
define('DASHBOARD_LOG_CRIT',     2);
define('DASHBOARD_LOG_ERR',      3);
define('DASHBOARD_LOG_WARNING',  4);
define('DASHBOARD_LOG_NOTICE',   5);
define('DASHBOARD_LOG_INFO',     6);
define('DASHBOARD_LOG_DEBUG',    7);


Plugin::setInfos(array(
    'id'          => 'dashboard',
    'title'       => 'Dashboard', 
    'description' => 'Keep up to date what is happening with your site.', 
    'version'     => '0.3.1', 
    'license'     => 'MIT',
    'author'      => 'Mika Tuupola',    
    'require_frog_version' => '0.9.5',
    'update_url'  => 'http://www.appelsiini.net/download/frog-plugins.xml',
    'website'     => 'http://www.appelsiini.net/projects/dashboard'
));

/* Stuff for backend. */
if (strpos($_SERVER['PHP_SELF'], 'admin/index.php')) {
    
    AutoLoader::addFolder(dirname(__FILE__) . '/models');
    AutoLoader::addFolder(dirname(__FILE__) . '/lib');
    
    Plugin::addController('dashboard', 'Dashboard');

    Observer::observe('page_edit_after_save',   'dashboard_log_page_edit');
    Observer::observe('page_add_after_save',    'dashboard_log_page_add');
    Observer::observe('page_delete',            'dashboard_log_page_delete');
    
    /* These currently only work in MIT fork (Toad) or 0.9.5 version of Frog. */
    Observer::observe('layout_after_delete',    'dashboard_log_layout_delete');
    Observer::observe('layout_after_add',       'dashboard_log_layout_add');
    Observer::observe('layout_after_edit',      'dashboard_log_layout_edit');
    
    Observer::observe('snippet_after_delete',    'dashboard_log_snippet_delete');
    Observer::observe('snippet_after_add',       'dashboard_log_snippet_add');
    Observer::observe('snippet_after_edit',      'dashboard_log_snippet_edit');

    Observer::observe('comment_after_delete',    'dashboard_log_comment_delete');
    Observer::observe('comment_after_add',       'dashboard_log_comment_add');
    Observer::observe('comment_after_edit',      'dashboard_log_comment_edit');
    Observer::observe('comment_after_approve',   'dashboard_log_comment_approve');
    Observer::observe('comment_after_unapprove', 'dashboard_log_comment_unapprove');
    
    Observer::observe('plugin_after_enable',     'dashboard_log_plugin_enable');
    Observer::observe('plugin_after_disable',    'dashboard_log_plugin_disable');

    Observer::observe('admin_login_success',     'dashboard_log_admin_login');
    Observer::observe('admin_login_failed',      'dashboard_log_admin_login_failure');
    Observer::observe('admin_after_logout',      'dashboard_log_admin_logout');
    
    Observer::observe('log_event',               'dashboard_log_event');
        
    function dashboard_log_page_add($page) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                        get_url('page/edit/' . $page->id), $page->title);
        $message      = __('Page <b>:title</b> was created by :name', 
                        array(':title' => $linked_title, 
                              ':name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    function dashboard_log_page_edit($page) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                        get_url('page/edit/' . $page->id), $page->title);
        $message      = __('Page <b>:title</b> was revised by :name', 
                        array(':title' => $linked_title, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    function dashboard_log_page_delete($page) {
        $message      = __('Page <b>:title</b> was deleted by :name', 
                        array(':title' =>  $page->title, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
  
    /* Layout */
    
    /* Frog triggers wrong event layout_after_edit ATM. */
    function dashboard_log_layout_delete($layout) {
        $message      = __('Layout <b>:title</b> was deleted by :name', 
                        array(':title' =>  $layout->name, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    function dashboard_log_layout_add($layout) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('layout/edit/' . $layout->id), $layout->name);
        $message      = __('Layout <b>:title</b> was created by :name', 
                        array(':title' =>  $linked_title, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_layout_edit($layout) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('layout/edit/' . $layout->id), $layout->name);
        $message      = __('Layout <b>:title</b> was revised by :name', 
                           array(':title' =>  $linked_title, 
                                 ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    /* Snippet */
    
    function dashboard_log_snippet_delete($snippet) {
        $message      = __('Snippet <b>:title</b> was deleted by :name', 
                        array(':title' =>  $snippet->name, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    function dashboard_log_snippet_add($snippet) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('snippet/edit/' . $snippet->id), $snippet->name);
        $message      = __('Snippet <b>:title</b> was created by :name', 
                        array(':title' =>  $linked_title, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_snippet_edit($snippet) {
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('snippet/edit/' . $snippet->id), $snippet->name);
        $message      = __('Snippet <b>:title</b> was revised by :name', 
                        array(':title' =>  $linked_title, 
                              ':name' => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_comment_add($comment) {
        
        /*
        TODO: It seems Page class here is NOT the model but the other Page class?
        $page = Page::findByIdFrom('Page', $comment->page_id);
        FIXME - This does not get called with SVN version of Frog?
        TODO: Fetch page title. 
        */
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('plugin/comment/edit/' . $comment->id), 'posted a comment');
        $message      = __(':name :title.', 
                        array(':name'  => $comment->author_name,
                              ':title' =>  $linked_title));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'comment');
    }

    function dashboard_log_comment_delete($comment) {
        
        /* TODO: Fetch page title. */
        $message      = __(':admin_name deleted comment by :author_name.', 
                        array(':author_name' => $comment->author_name,
                              ':admin_name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'comment');
    }

    function dashboard_log_comment_approve($comment) {
        
        /* TODO: Fetch page title. */
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                 get_url('plugin/comment/edit/' . $comment->id), 'comment');
        $message      = __(':admin_name approved :title by :author_name.', 
                        array(':author_name' => $comment->author_name,
                              ':title'       => $linked_title,
                              ':admin_name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'comment');
    }

    function dashboard_log_comment_unapprove($comment) {
        
        /* TODO: Fetch page title. */
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                 get_url('plugin/comment/edit/' . $comment->id), 'comment');
        $message      = __(':admin_name rejected :title by :author_name.', 
                        array(':author_name' => $comment->author_name,
                              ':title'       => $linked_title,
                              ':admin_name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'comment');
    }

    function dashboard_log_comment_edit($comment) {
        
        /* TODO: Fetch page title. */
        $linked_title = sprintf('<a href="%s">%s</a>', 
                                    get_url('plugin/comment/edit/' . $comment->id), 'comment');
        $message      = __(':admin_name edited :title by :author_name.', 
                        array(':author_name' => $comment->author_name,
                              ':title'       => $linked_title,
                              ':admin_name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'comment');
    }
    
    function dashboard_log_plugin_enable($plugin) {
        $message      = __('Plugin <b>:title</b> was enabled by :name', 
                        array(':title' => $plugin,
                              ':name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_plugin_disable($plugin) {        
        $message      = __('Plugin <b>:title</b> was disabled by :name', 
                        array(':title' => $plugin, 
                              ':name'  => AuthUser::getRecord()->name));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_admin_login($username) {
        $message      = __('User <b>:name</b> logged in.', 
                        array(':name'  => $username));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }

    function dashboard_log_admin_login_failure($username) {
        $message      = __('User <b>:name</b> failed logging in.', 
                        array(':name'  => $username));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_admin_logout($username) {
        $message      = __('User <b>:name</b> logged out.', 
                        array(':name'  => $username));
        dashboard_log_event($message, DASHBOARD_LOG_NOTICE, 'core');
    }
    
    function dashboard_log_event($message, $priority=DASHBOARD_LOG_NOTICE, $ident='misc') {
        $data['ident']    = $ident;
        $data['priority'] = $priority;
        $data['message']  = $message;
        $entry = new DashboardLogEntry($data);
        $entry->save();
    }

} 
