<?php
class ManagerController extends Controller {
    /**
     * endpoint for label/manager/users 
     */
    public function index() { 
        $this->allow_label();
        $this->setTitle(l('manage-users'));
        $this->addBreadCrumb(l('manage-users'));
        $this->activeMenu = 'manage_artists';

        $random_pass = time(strtotime(time(strtotime('NOW').strtotime(rand(1000,9000))).rand(1000,9000)));

        $type = 2;
        $users = $this->model('manager::manager')->lists($type, $this->request->input('term', null), model('user')->authId);
        $message = null;
        if ($val = $this->request->input('val')) {
            $val['password'] = $random_pass;
            if ($this->isDemo()) $this->defendDemo();
            $required = array(
                'full_name' => 'required',
                'username' => 'required|alphanum|unique:users',
                'password' => 'required',
                'email' => 'required|email|unique:users'
            );
            $required = Hook::getInstance()->fire('manager.user.required.data', $required, array($val));
            $val  = Hook::getInstance()->fire('manager.admin.user.data', $val);
            $validator = Validator::getInstance()->scan($val, $required);

            if ($validator->passes()) {
                $userid = $this->model('user')->addUser($val, true);

                return json_encode(array(
                    'type' => 'url',
                    'value' => url("label/manager/users"),
                    'message' => l('user-created')
                ));
            } else {
                $message = $validator->first();
                return json_encode(array(
                    'type' => 'error',
                    'message' => $message
                ));
            }
        }
        return $this->render($this->view('manager::management/lists', array('type' => $type, 'users' => $users, 'message' => $message)),true);
    } 

    /**
     * endpoint for label/manager/users/edit
     */
    public function userEdit() {
        $this->allow_label($this->request->input('id'));
        $this->activeMenu = 'manage_artists';
        $this->setTitle(l('edit-user'));
        $this->addBreadCrumb(l('edit-user'));
        $user = $this->model('user')->getUser($this->request->input('id'));

        if (!$user) $this->request->redirectBack();
        $message = null;
        $messageType = 'danger';

        if ($val = $this->request->input('val')) {
            if ($this->isDemo()) $this->defendDemo();
            $required = array(
                'full_name' => 'required',
                'username' => 'required|username|predefined|alphanum',
                'email' => 'required|email'
            );
            $required = Hook::getInstance()->fire('manager.user.required.data', $required, array($val));
            $val  = Hook::getInstance()->fire('manager.admin.user.data', $val);
            $required['username'] = 'required|username|predefined|alphanum';

            unset($required['email']);

            $validator = Validator::getInstance()->scan($val, $required);

            if ($validator->passes()) {
                if ($user['username'] != $val['username']) {
                    //check for other users with same name
                    if (model('user')->otherUseUsername($val['username'], $user['id'])) {
                        return json_encode(array(
                            'message' => l('selected-username-is-used-by-others'),
                            'type' => 'error'
                        ));
                    }
                }

                if($val['email'] and $val['email'] != $user['email']) {
                    if (model('user')->otherUseEmail($val['email'], $user['id'])) {
                        return json_encode(array(
                            'message' => l('selected-email-is-used-by-others'),
                            'type' => 'error'
                        ));
                    }
                }
                $user = $this->model('user')->getUser($this->request->input('id')); 
                $val['is_label'] = 0;
                $this->model('user')->saveUser($val, $user);
                $message = l('user-saved-success');
                $messageType = 'success';
                $user = $this->model('user')->getUser($this->request->input('id'));
                return json_encode(array(
                    'type' => 'success',
                    'message' => l('user-saved')
                ));
            } else {
                $message = $validator->first();
                return json_encode(array(
                    'type' => 'error',
                    'message' => $message
                ));
            }

        }

        return $this->render($this->view('manager::management/edit', array('user' => $user, 'message' => $message,'messageType' => $messageType)),true);
    }

    /**
     * endpoint for label/manager/users/edit/photo
     */
    public function userPictureEdit() {
        $this->allow_label($this->request->input('id'));
        $this->activeMenu = 'manage_artists';
        $this->setTitle(l('manager::edit_profile_picture'));
        $this->addBreadCrumb(l('edit-user'));
        $user = $this->model('user')->getUser($this->request->input('id'));

        if (!$user) $this->request->redirectBack();
        $message = null;
        $messageType = 'danger';

        if ($this->request->inputFile('avatar') or $this->request->inputFile('cover')) {
            $avatar = $this->request->inputFile('avatar');
            $cover = $this->request->inputFile('cover');
            if ($avatar) {
                
                $uploader = new Uploader($avatar);
                $uploader->setPath('avatar/'.$user['id'].'/');
                if ($uploader->passed()) {
                    $avatar = $uploader->resize()->result(); 
                    $this->model('user')->saveUser(array('avatar' => $avatar), $user);
                } else {
                    return json_encode(array('type' => 'error', 'message' => $uploader->getError()));
                }
            }

            if ($cover) {
                $uploader = new Uploader($cover);
                $uploader->setPath('cover/'.$user['id'].'/');
                if ($uploader->passed()) {
                    $cover = $uploader->uploadFile()->result(); 
                    $this->model('user')->saveUser(array('cover' => $cover), $user);
                } else {
                    return json_encode(array('type' => 'error', 'message' => $uploader->getError()));
                }
            }

            return json_encode(array(
                'type' => 'success',
                'message' => l('account-settings-saved')
            ));
        }

        return $this->render($this->view('manager::management/edit_picture', array('user' => $user, 'message' => $message,'messageType' => $messageType)),true);
    }

    /**
     * endpoint for label/manager/tracks 
     */
    public function tracks() {
        $this->allow_label($this->request->input('user'));
        $this->setTitle(l('tracks'));
        $this->addBreadCrumb(l('tracks'));
        $this->activeMenu = 'manage_artists';
        $genre = $this->request->input('genre', '');
        $user = $this->request->input('user', '');
        $term = $this->request->input('term', '');
        if ($action= $this->request->input('action') == 'delete') {
            if ($this->isDemo()) $this->defendDemo();
            $this->model('admin')->deleteTrack($this->request->input('id'));
        }

        if ($val = $this->request->input('val')) {
            if ($this->isDemo()) $this->defendDemo();
            $track = $this->model('track')->findTrack($val['id']);
            $validator = Validator::getInstance()->scan($val, array(
                'titles' => 'required',
                'tags' => 'required'
            ));
            if ($validator->passes()) {
                if ($val['day'] and $val['month'] and $val['year']) {
                    $val['release'] = date("Y-m-d", mktime(0, 0, 0, $val['month'], $val['day'], $val['year']));
                } else {
                    $val['release'] = '';
                }

                $artFile = $this->request->inputFile('img');
                if ($artFile) {
                    $artUpload = new Uploader($artFile);
                    $artUpload->setPath("tracks/".$this->model('user')->authId.'/art/'.date('Y').'/');
                    if ($artUpload->passed()) {
                        $val['art'] = $artUpload->resize()->result();
                    } else {
                        return json_encode(array(
                            'message' => $artUpload->getError(),
                            'type' => 'error'
                        ));
                    }
                }

                if ($lyricsFile = $this->request->inputFile('lyrics_file')) {
                    $upload = new Uploader($lyricsFile, 'file');
                    $upload->setPath("tracks/".$this->model('user')->authId.'/lyric/'.date('Y').'/');
                    if ($upload->passed()) {
                        $val['lyrics'] = $upload->uploadFile()->result();
                    } else {
                        return json_encode(array(
                            'message' => l('lyric-file-error'),
                            'type' => 'error'
                        ));
                    }
                }

                $this->model('track')->add($val, $track, true);

                return json_encode(array(
                    'type' => 'modal-url',
                    'value' => getFullUrl(true),
                    'message' => l('saved-success'),
                    'content' => "#editTrackModal-".$val['id']
                ));
            } else {
                return json_encode(array(
                    'message' => $validator->first(),
                    'type' => 'error'
                ));
            }

        }
        $tracks = $this->model('admin')->getTracks($genre, $term, $user);
        return $this->render($this->view('admin/tracks/index', array('tracks' => $tracks, 'genre' => $genre, 'term' => $term, 'user' => $user)), true);
    }

    public function userAction() {
        $this->allow_label($this->request->input('id'));
        $action = $this->request->input('action');
        $id = $this->request->input('id');
        if ($this->isDemo()) $this->defendDemo();
        switch($action) {
            case 'ban':
                $this->model('user')->ban($id);
                break;
            case 'unban':
                $this->model('user')->unban($id);
                break;
            case 'delete':
                $this->model('user')->delete($id);
                break;
        }

        return $this->request->redirectBack();
    }

    function allow_label($user = 0) {
        if ($user) {
            if ($this->model('user')->authId === $this->model('user')->getUser($user)['label']) return true; 
        } else {
            if ($this->model('user')->getUser($this->model('user')->authId)['is_label']) return true; 
        }
        $this->request->redirect(url());
    }
}

function set_flashdata($key = '', $value = '') { 
    $_SESSION[$key] = $value; 
}

function read_flashdata($key = '') { 
    if (isset($_SESSION[$key])) {
        $flash = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $flash;
    } 
}
