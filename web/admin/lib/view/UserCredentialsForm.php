<?php
namespace lib\view;

use lib\domain\SilverbulletFactory;
use lib\view\html\Button;
use lib\view\html\Row;
use lib\view\html\Table;
use lib\domain\SilverbulletUser;
use lib\domain\SilverbulletCertificate;
use lib\view\html\Tag;

class UserCredentialsForm implements PageElement{
    
    const EDITABLEBLOCK_CLASS = 'sb-editable-block';
    const TITLEROW_CLASS = 'sb-title-row';
    const USERROW_CLASS = 'sb-user-row';
    const ADDNEWUSER_CLASS = 'sb-add-new-user';
    const RESET_BUTTON_ID = 'sb-reset-dates';
    
    /**
     *
     * @var Table
     */
    private $table;

    /**
     *
     * @var TitledFormDecorator
     */
    private $decorator;
    
    /**
     * @var string
     */
    private $action;
    
    /**
     * 
     * @param string $title
     * @param SilverbulletFactory $factory
     */
    public function __construct($title, $factory) {
        $this->action = $factory->addQuery($_SERVER['SCRIPT_NAME']);
        $this->table = new Table();
        $this->table->addAttribute("cellpadding", 5);
        $this->decorator = new TitledFormDecorator($this->table, $title, $this->action);
        $this->decorator->addHtmlElement(new Button(_('Save'),'submit', SilverbulletFactory::COMMAND_SAVE, SilverbulletFactory::COMMAND_SAVE));
        $this->decorator->addHtmlElement(new Button(_('Reset'),'reset', '', '', 'delete', self::RESET_BUTTON_ID));
        $this->addTitleRow();
    }
    
    /**
     * 
     */
    private function addTitleRow(){
        $row = new Row(array('user' => 'User', 'token' => 'Token/Certificate details', 'expiry' => 'User Expiry/Certificate Expiry', 'action' => 'Actions'));
        $row->addAttribute('class', self::TITLEROW_CLASS);
        $this->table->addRow($row);
    }
    
    /**
     * 
     * @param SilverbulletUser $user
     */
    public function addUserRow($user){
        $row = new Row(array('user' => $user->getUsername(), 'expiry' => new DatePicker(SilverbulletFactory::PARAM_EXPIRY_MULTIPLE, $user->getExpiry()) ));
        $row->addAttribute('class', self::USERROW_CLASS);
        $index = $this->table->size();
        $this->table->addRow($row);
        $hiddenUserId = new Tag('input');
        $hiddenUserId->addAttribute('type', 'hidden');
        $hiddenUserId->addAttribute('name', SilverbulletFactory::PARAM_ID_MULTIPLE);
        $hiddenUserId->addAttribute('value', $user->getIdentifier());
        $this->table->addToCell($index, 'user', $hiddenUserId);
        $this->table->addToCell($index, 'action', new Button(_('Delete User'),'submit', SilverbulletFactory::COMMAND_DELETE_USER, $user->getIdentifier(), 'delete'));
        $this->table->addToCell($index, 'action', new Button(_('New Credential'),'submit', SilverbulletFactory::COMMAND_ADD_CERTIFICATE, $user->getIdentifier()));
    }
    
    /**
     * 
     * @param SilverbulletCertificate $certificate
     */
    public function addCertificateRow($certificate){
        $index = $this->table->size();
        $this->table->addRowArray(array('token' => $certificate->getCertificateDetails(), 'expiry' => $certificate->getExpiry()));
        $this->table->addToCell($index, 'action', new Button(_('Revoke'), 'submit', SilverbulletFactory::COMMAND_REVOKE_CERTIFICATE, $certificate->getIdentifier(), 'delete'));
    }
    
    public function render(){
        ?>
        <div class="<?php echo self::EDITABLEBLOCK_CLASS;?>">
            <?php $this->decorator->render();?>
            <form method="post" action="<?php echo $this->action;?>" accept-charset="utf-8">
                <div class="<?php echo self::ADDNEWUSER_CLASS; ?>">
                    <label for="<?php echo SilverbulletFactory::COMMAND_ADD_USER; ?>"><?php echo _("Please enter a username of your choice and user expiry date to create a new user:"); ?></label>
                    <div>
                        <input type="text" name="<?php echo SilverbulletFactory::COMMAND_ADD_USER; ?>">
                        <?php 
                            $datePicker = new DatePicker(SilverbulletFactory::PARAM_EXPIRY);
                            $datePicker->render(); 
                        ?>
                    </div>
                    <button type="submit" ><?php echo _('Add new user'); ?></button>
                </div>
            </form>
        </div>
        <?php
    }
}
