<?php


class HomeLib
{

    public static function info($i, $class = '')
    {

        $html = "<br>";
        $html .= $i['user'];
        $html .= "<br>";
        $html .= $i['first_number'] . " ";
        $html .= Utils::operacao($i['operator']) . " ";
        $html .= $i['second_number'];
        $html .= " = " . $i['result'];
        $html .= "<br>";
        $html .= "==========";

        return $html;
    }

    public static function card($i, $class = '', $link='')
    {
        $html = "";
        $html .= "<div class='demo-card-wide mdl-card mdl-shadow--2dp $class'>
        <div class='mdl-card__title'>
            <h2 class='mdl-card__title-text'>$i</h2>
        </div>
        <div class='mdl-card__supporting-text'>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            Mauris sagittis pellentesque lacus eleifend lacinia...
        </div>
        <div class='mdl-card__actions mdl-card--border'>
            <a href='$link' class='mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect'>
                Get Started
            </a>
        </div>
        <div class='mdl-card__menu'>
            <button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'>
                <i class='material-icons'>share</i>
            </button>
        </div>
    </div>";
    return $html;
    }
}
