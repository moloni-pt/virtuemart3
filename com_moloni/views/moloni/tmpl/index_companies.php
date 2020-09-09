<?php

use Moloni\Functions\Base;

defined('_JEXEC') or die('Restricted access');

$companies = Base::selectCompanies();
?>

    <div class='outBoxEmpresa'>
<?php if (!empty($companies)) : ?>
    <?php foreach ($companies as $key => $company): ?>
        <div class="caixaLoginEmpresa"
             onclick="window.location.href='index.php?option=com_moloni&company_id=<?php echo $company['company_id']; ?>'"
             title="Login/Entrar">
            <div class="caixaLoginEmpresa_logo">
            <span>
            <?php if (trim($company['image']) !== '') : ?>
                <?php echo '<img src="https://www.moloni.com/_imagens/?macro=imgAC_iconeEmpresa_s2&amp;img='
                    . $company['image'] . '" alt="'
                    . $company['name'] . '" style="margin:0 10px 0 0; vertical-align:middle;">';
                ?>
            <?php endif; ?>
                </span>
            </div>
            <span class="t14_b"> <?php echo $company['name']; ?></span>
            <br> <?php echo $company['address']; ?>
            <br> <?php echo $company['zip_code']; ?>
            <p><b>Contribuinte</b>: <?php echo $company['vat']; ?></p>
        </div>


    <?php endforeach; ?>
<?php else : ?>
    </div>
    <div class="msgAlertaForms3">O utilizador não tem empresas disponíveis!
        <a type="button" class="btn btn-big btn-danger" href="index.php?option=com_moloni&action=logout">Voltar</a>
    </div>
<?php endif; ?>