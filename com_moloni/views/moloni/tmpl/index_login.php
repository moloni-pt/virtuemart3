<?php

use Moloni\Functions\Messages;
use Moloni\Functions\MoloniDb;

defined('_JEXEC') or die('Restricted access');
MoloniDb::defineConfigs();
?>

<div id='formLogin'>
    <a href='https://moloni.com/dev/' target='_BLANK'>
        <img src='https://www.moloni.com/_imagens/_tmpl/bo_logo_topo_01.png' width='300px'>
    </a>
    <form id='formPerm' method='POST' action=''>
        <table>
            <tr>
                <td><label for='username'>Utilizador/Email</label></td>
                <td><input type='text' name='user' id='username'></td>
            </tr>
            <tr>
                <td><label for='password'>Password</label></td>
                <td><input type='password' name='pass' id='password'></td>
            </tr>
            <?php if(!empty(Messages::$messages['login'])) :?>
                <tr>
                    <td></td>
                    <td style='text-align: center;'> Utilizador/Password Errados</td>
                </tr>
            <?php endif; ?>
            <tr>
                <td></td>
                <td>
                    <input type='submit' name='submit' value='login'>
                    <input type='reset' name='limpar' value='limpar'>
                    <span class='goRight power'>Powered by: Moloni API</span>
                </td>
            </tr>
        </table>
    </form>
</div>

