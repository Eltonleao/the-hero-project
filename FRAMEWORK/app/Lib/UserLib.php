<?php


class UserLib
{

    public  static function getLicaoRow($l)
    {

        $html = "";
        $html .= '
          <tr>
          <td> <b>Lição - '.$l['numero'].'</b></td>
          <td> ' .  ucfirst($l['nome']) .'</td>
          <td><span class="material-icons">
          <a href="'.URL.'user">article<a>
          </span></td>
          </tr>
        ';

        return $html;
    }
}
