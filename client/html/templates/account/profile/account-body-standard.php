<?php

$enc = $this->encoder();

/** client/html/account/profile/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/url/config
 */

/** client/html/account/profile/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * For SEO, it's nice to have URLs which contains only required parameters.
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2019.10
 * @category User
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

$passwordErrors = $this->get('passwordErrors', []);

?>
<?php $this->block()->start( 'account/profile/account' ) ?>

<div class="account-profile-account">
    <h1 class="header"><?= $enc->html( $this->translate( 'client', 'account' ) ) ?></h1>
    <form class="container-fluid" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/profile/url' ) ) ?>">
        <?= $this->csrf()->formfield() ?>

            <div class="row password-change-notifications">
                <?php if ( $this->get('passwordChanged', null) === true ) : ?>
                    <h2 class="text-success"><?= $this->translate('client', 'Password changed successfull!') ?></h2>
                <?php elseif ($this->get('passwordChanged', null) === false) : ?>
                    <h2 class="text-danger"><?= $this->translate('client', 'Error(s) occured!') ?></h2>
                <?php endif ?>
            </div>
        <div class="row">
            <div class="password col-lg-12">
                <h2 class="header"><?= $enc->html( $this->translate( 'client', 'Password' ) ) ?></h2>
                <div class="panel panel-default password-change">
                    <div class="form-item form-group row old-password">
                        <label class="col-md-4" for="old-password">
                            <?= $enc->html( $this->translate( 'client', 'Old password' ), $enc::TRUST ) ?>
                        </label>
                        <div class="col-md-8">
                            <input class="form-control" type="password"
                                   id="old-password"
                                   name="<?= $enc->attr( $this->formparam( array( 'account', 'customer.oldpassword' ) ) ) ?>"
                                   placeholder="<?= $enc->attr( $this->translate( 'client', 'Old password' ) ) ?>"
                            >
                            <?php if( isset($passwordErrors['oldPassword']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['oldPassword'])) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                    </div>
                    <div class="form-item form-group row new-password">

                        <label class="col-md-4" for="new-password">
                            <?= $enc->html( $this->translate( 'client', 'New password' ), $enc::TRUST ) ?>
                        </label>
                        <div class="col-md-8">
                            <input class="form-control" type="password"
                                   id="new-password"
                                   name="<?= $enc->attr( $this->formparam( array( 'account', 'customer.newpassword' ) ) ) ?>"
                                   placeholder="<?= $enc->attr( $this->translate( 'client', 'New password' ) ) ?>"
                            >
                            <?php if( isset($passwordErrors['confirm']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['confirm'])) ?></strong>
                                </span>
                            <?php endif ?>
                            <?php if( isset($passwordErrors['isNew']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['isNew'])) ?></strong>
                                </span>
                            <?php endif ?>
                            <?php if( isset($passwordErrors['passwordRules']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['passwordRules'])) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                    </div>
                    <div class="form-item form-group row old-password">
                        <label class="col-md-4" for="confirm-new-password">
                            <?= $enc->html( $this->translate( 'client', 'Confirm password' ), $enc::TRUST ) ?>
                        </label>
                        <div class="col-md-8">
                            <input class="form-control" type="password"
                                   id="confirm-new-password"
                                   name="<?= $enc->attr( $this->formparam( array( 'account', 'customer.confirmnewpassword' ) ) ) ?>"
                                   placeholder="<?= $enc->attr( $this->translate( 'client', 'Confirm password' ) ) ?>"
                            >
                            <?php if( isset($passwordErrors['confirm']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['confirm'])) ?></strong>
                                </span>
                            <?php endif ?>
                            <?php if( isset($passwordErrors['isNew']) ) : ?>
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong><?= $enc->html($this->translate('client', $passwordErrors['isNew'])) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                    </div>
                </div>
                <div class="password-button-group">
                    <button class="btn btn-cancel" value="1" type="reset">
                        <?= $enc->html( $this->translate( 'client', 'Cancel' ), $enc::TRUST ) ?>
                    </button>
                    <button class="btn btn-primary btn-save" value="1" name="<?= $enc->attr( $this->formparam( array( 'account', 'save' ) ) ) ?>">
                        <?= $enc->html( $this->translate( 'client', 'Change password' ), $enc::TRUST ) ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
