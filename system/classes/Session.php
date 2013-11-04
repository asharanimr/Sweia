<?php

    /**
     * @author Joshua Kissoon
     * @date 20121212
     * @description Manages sessions throughout the site
     */
    class Session
    {

       private $logged_in = false;
       public $uid, $username;
       private $ipaddress, $sid, $status;

       function __construct()
       {
          /* When this class is initialized, we automatically create a new session */
          $this->createSession();
       }

       public function loginUser($cuser)
       {
          /* Here we check if this is a valid user, then generate a new session id and login this user */
          if (!User::isUser($cuser->uid))
          {
             /* If this user object is not valid */
             ScreenMessage::setMessage("Sorry login Failed", "error");
             redirect_to(BASE_URL);
          }

          /* If the user is allowed, then log them in */
          session_regenerate_id(true);
          $this->uid = $_SESSION['uid'] = $cuser->uid;
          $this->logged_in = $_SESSION['logged_in'] = true;
          $this->username = $_SESSION['logged_in_username'] = $cuser->username;

          /* Add the necessary data to the class */
          $this->ipaddress = $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
          $this->sid = session_id();
          $this->status = $_SESSION['status'] = 1;

          /* Now we create the necessary cookies for the user and save the session data */
          setcookie("jsmartsid", $this->sid, time() + 3600 * 300, "/");

          /* Save the entire session data to the database */
          $args = array(
              "::uid" => $this->uid,
              "::sid" => $this->sid,
              "::ipaddress" => $this->ipaddress,
              "::status" => $this->status,
              "::data" => json_encode($_SESSION),
          );
          global $DB;
          $DB->query("INSERT INTO user_sessions (uid, sid, ipaddress, status, data) VALUES('::uid', '::sid', '::ipaddress', '::status', '::data')", $args);
       }

       public function loadDataFromCookies()
       {
          /* Here we try to load the user's data from cookies */
          if (!@$_COOKIE['jsmartsid'])
             return false;

          /* Update the database sessions for this user */
          $this->updateSessions();

          /* If there is a cookie, check if there exists a valid database session and load it */
          global $DB;
          $res = $DB->query("SELECT * FROM user_sessions WHERE sid='::sid' AND status='1' LIMIT 1", array("::sid" => $_COOKIE['jsmartsid']));
          if ($DB->resultNumRows() < 1)
             return false;

          $user_session = $DB->fetchObject($res);

          /* Now load all of the data into session, generate a new sid and update it in the database */
          $data = json_decode($user_session->data);
          foreach ($data as $key => $value)
             $_SESSION[$key] = $value;
          $cuser = new User($user_session->uid);
          $this->uid = $_SESSION['uid'] = $cuser->uid;
          $this->logged_in = $_SESSION['logged_in'] = true;
          $this->username = $_SESSION['logged_in_username'] = $cuser->username;

          /* Add the necessary data to the class */
          $this->ipaddress = $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
          session_regenerate_id(true);
          $this->sid = session_id();
          $this->status = $_SESSION['status'] = 1;

          /* Reset the session id */
          setcookie("jsmartsid", $this->sid, time() + 3600 * 300, "/");

          /* update the session id to the database */
          $args = array(
              "::usid" => $user_session->usid,
              "::sid" => $this->sid,
          );
          return $DB->query("UPDATE user_sessions SET sid = '::sid' WHERE usid='::usid'", $args);
       }

       public function logoutUser()
       {
          /* Here we logout the user, set the session variables to false and destroy the session */

          global $DB;
          /* Set the session's status to 0 in the database */
          $DB->query("UPDATE user_sessions SET status = '0' WHERE sid='::sid'", array("::sid" => session_id()));

          unset($_SESSION['uid']);
          unset($this->uid);
          $this->logged_in = $_SESSION['logged_in'] = false;
          $this->destroySession();
       }

       private function updateSessions()
       {
          /* Invalidate all sessions for this user which have passed the session lifetime of the site */
          global $DB;
          $session_lifetime = JSmart::variableGet("session_lifetime");
          $old_session_ts = time() - $session_lifetime;
          $old_session_dt = date("Y-m-d H:i:s", $old_session_ts);
          $sql = "UPDATE user_sessions SET status='0' WHERE create_ts < '$old_session_dt'";
          return $DB->query($sql);
          exit;
       }

       public function isLoggedIn()
       {
          return valid(@$_SESSION['logged_in']);
       }

       public function loggedInUid()
       {
          /*
           * Returns the uid of the logged in user
           */
          return $_SESSION['uid'];
       }

       private function createSession()
       {
          /* Here we create a new session */
          session_start();
       }

       private function destroySession()
       {
          /* Here we destroy the current session */
          session_destroy();
       }

    }

    $SESSION = $session = new Session();