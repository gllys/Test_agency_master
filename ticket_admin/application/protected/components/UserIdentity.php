<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
require_once dirname(__FILE__) . '/../extensions/phpPasswordHashingLib/passwordLib.php';

class UserIdentity extends CUserIdentity {

    /**
     * 返回加密的密码，不可逆的加密方式
     *
     * @param string $password 密码
     * @return string
     * */
    public function getHashedPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, array('cost' => 8));
    }

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        $result = Users::model()->find('account=:account AND deleted_at IS NULL', array(
            ':account' => trim($this->username)
        ));

        if (!password_verify(trim($this->password), $result['password'])) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;

            // 用户状态为0  表示已经停用
            if ($result->status == 0) {
                $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
            } else {
                $this->setState('uid', $result->id);
                $this->setState('display_name', isset($result->name) ? $result->name : $result->account);
                $this->setState('account', $result->account);
                $this->setState('is_super', $result->is_super);
            }
        }
        return !$this->errorCode;
    }

}
