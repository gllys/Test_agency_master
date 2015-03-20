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
            ':account' => $this->username
        ));

        if (!password_verify($this->password, $result['password'])) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;

            // 用户状态为0  表示已经停用
            if ($result->status == 0) {
                $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
            } else {
                if (empty($result->organization_id) || is_null($result->organization_id) || !isset($result->organization_id)) {
                    $result->organization_id = 0;
                } else {
                    //查看机构是否启用
                    $rs = Organizations::api()->show(array('id' => $result->organization_id), 0);
                    if (ApiModel::isSucc($rs)) {
                        $data = ApiModel::getData($rs);
                        if ($data['status'] == 0) {
                            $this->errorCode = 3;
                            return !$this->errorCode;
                        }
                    } else {
                        $this->errorCode = 3;
                        return !$this->errorCode;
                    }
                }
                $this->setState('uid', $result->id);
                $this->setState('display_name', isset($result->name) ? $result->name : $result->account);
                $this->setState('account', $result->account);
                $this->setState('org_id', $result->organization_id);
                $this->setState('is_super', $result->is_super);
                $this->setState('created_at', $result->created_at);
            }
        }
        return !$this->errorCode;
    }

}
