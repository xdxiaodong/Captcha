<?php
require_once __DIR__.'/../vendor/autoload.php';
use Gregwar\Captcha\PhraseBuilder;

// We need the session to check the phrase after submitting
session_start();
?>

<html>
<?php
/**
 * 验证码校验
 *
 * @return array
 */
function captcha_validate()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        \session_start();
        if (!isset($_SESSION['captcha_code'])) {
            return array(false, '验证码错误');
        }
        $captcha_code = $_SESSION['captcha_code'];
        $captcha_time = $_SESSION['captcha_time'];
        unset($_SESSION['captcha_code']);
        unset($_SESSION['captcha_time']);
        if (isset($captcha_code) && isset($captcha_time)) {
            // 验证码有效期校验
            if (time() - intval($captcha_time) >= 120) {
                // return array(false, '验证码已过期');
                return array(false, '验证码错误');
            }
            // 验证码校验
            if (PhraseBuilder::comparePhrases($captcha_code, $_POST['captcha'])) {
                // 验证成功
                return array(true);
            }
        } else {
            // 验证失败
            return array(false, '验证码错误');
        }
    }
    return array(false, '验证码错误');
}
// 验证码验证
list($success, $message) = captcha_validate();
if ($success) {
    echo '验证码验证成功';
} else {
    echo $message;
}
?>
<form method="post">
    Copy the CAPTCHA:
    <?php
            // See session.php, where the captcha is actually rendered and the session phrase
            // is set accordingly to the image displayed
        ?>
    <img src="session.php" />
    <input type="text" name="phrase" />
    <input type="submit" />
</form>

</html>