<?php
/**
 * Flash Helper to display flash messages.
 * Usage (Sweet Alert):    flash()->success('Title', 'Message')
 *                         flash()->error('Title', 'Message')
 *                         flash()->info('Title', 'Message')
 *                         flash()->confirm('Title', 'Message', 'Level ex: success, error, info (Optional)', 'ButtonText (Optional)')
 *                         
 * Usage (Basic Flash):    flash()->basicSuccess('Message')
 *                         flash()->basicWarning('Message')
 *                         flash()->basicInfo('Message')
 *                         
 * @param  [String] $title      [Title of the Message]
 * @param  [String] $message    [Message of the flash]
 * @param  [String] $buttonText [Text to display on a button]
 * @return [Boolean]            [Flash]
 */
function flash()
{

     $flash = app('App\Http\Flash');
     return $flash;
}

function randomString($strLength)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

     $string = '';
     for ($i = 0; $i < $strLength; $i++) {
          $string .= $characters[rand(0, strlen($characters) - 1)];
     }

     return $string;
}

/**
 * [Determines whether the permission passed in is denied or not]
 * @param  [string] $permission [The permission we are checking]
 * @return [boolean]             
 */
function deniedPermission($permission)
{
    if(Gate::denies($permission))
    {
        return true;
    }
        return false;    
}

function linkify($text){
    return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" target="_blank">$1</a>', $text);
}

function rand_color() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

function sort_by($column, $body, $route)
{
     // $direction = ($_GET['direction'] == 'asc' ? 'desc' : 'asc');
     return link_to_route($route, $body, ['8', 'sortBy' => $column]);
}

function view_sort_by($column, $body, $route, $viewId, $search = null)
{
     $direction = (Request::get('direction') == 'asc' ? 'desc' : 'asc');
     if($search) {
          return link_to_route($route, $body, [$viewId, 'sortBy' => $column, 'direction' => $direction, 'search' => $search]);
     }
     return link_to_route($route, $body, [$viewId, 'sortBy' => $column, 'direction' => $direction]);
}

function deSnake($value) {
     return ucwords(str_replace('_', ' ', $value));
}