<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use Carbon\Carbon;
use App\Job;
use App\IPB;
use App\Member;
use App\Article;
use App\Interview;
use App\Settings;
use App\ForumOnMain;
use App\Photogallery;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $forum_on_main = ForumOnMain::orderBy('position', 'asc')->get();
        $interview_on_main = Interview::published()->where('main_slider', 1)->orderBy('main_slider_position', 'asc')->get();

        $articles_on_main_slider = intval(trim(Settings::byKey('articles_on_main_slider')->value)) * 3;

		$news = News::published()->take(10)->get();

        $articles_count = 9 + $articles_on_main_slider - $forum_on_main->count() - $interview_on_main->count();

        $articles = Article::published()->take($articles_count)->get();
        $interviews = Interview::published()->take(2)->get();

        $photogalleries = Photogallery::published()->take(6)->get();

        $forums = Cache::get('forum.forums_on_main', function () use ($forum_on_main) {

            $forums = [];

            $ids = explode(',', trim(Settings::getByKey('forums_topics'), ','));

            foreach ($forum_on_main as $forum) {
                $topic = IPB::api('forums/topics/' . $forum->forum_id);
                $topic['posts'] = $topic['posts'] - 1;
                $forums[$forum->position] = $topic;
                $forums[$forum->position]['image'] = $forum->image;
                $forums[$forum->position]['big_on_main_slider'] = $forum->big_on_main_slider;
                $forums[$forum->position]['sm_on_main_slider'] = $forum->sm_on_main_slider;
            }

            Cache::add('forum.forums_on_main', $forums, 60);

            return $forums;
        }, 60);


        $total = count($articles) + count($forums) + count($interview_on_main);

        $articles_data = [];

        for ($i = 0, $j = 0, $k = 0; $i < $total; $i++) {
            if (isset($forums[$i+1])) {
                $articles_data[] = [
                    'type' => 'forum',
                    'data' => $forums[$i+1]
                ];

                unset($forums[$i+1]);
                $j++;
            } elseif (isset($interview_on_main[$k]) && $interview_on_main[$k]->main_slider_position == ($i+1)) {
                $articles_data[] = [
                    'type' => 'interview',
                    'data' => $interview_on_main[$k]
                ];
                $k++;
            } else {
                $articles_data[] = [
                    'type' => 'article',
                    'data' => $articles[$i - $j - $k]
                ];
            }
        }

		$jobs = Job::published()->latest()->take(5)->get();

        return view('home', compact('news', 'articles_data', 'articles', 'interviews', 'articles_on_main_slider', 'forums', 'jobs', 'photogalleries'));
    }
}
/*
 * Authorization
 * @return \IPS\Member|false
 */
function doLogin($username, $password, $rememberMe=true, $anonymous=false)
{
    $login = new \IPS\Login(\IPS\Http\Url::internal(''));
    $login->forms();

    try {
        $member = $login->authenticateStandard(array(
            'auth'     => $username,
            'password' => $password,
        ));
    } catch (\IPS\Login\Exception $e) {
        return false;
    }

    if ($anonymous and !\IPS\Settings::i()->disable_anonymous) {
        \IPS\Session::i()->setAnon();
        \IPS\Request::i()->setCookie('anon_login', 1);
    }

    \IPS\Session::i()->setMember($member);

    if ($rememberMe) {
        $expire = new \IPS\DateTime;
        $expire->add(new \DateInterval('P7D'));
        \IPS\Request::i()->setCookie('member_id', $member->member_id, $expire);
        \IPS\Request::i()->setCookie('pass_hash', $member->member_login_key, $expire);

        if ($anonymous and !\IPS\Settings::i()->disable_anonymous) {
            \IPS\Request::i()->setCookie('anon_login', 1, $expire);
        }
    }

    $member->memberSync('onLogin', array( \IPS\Login::getDestination() ));

    return $member->apiOutput();
}

/*
 * Logout
 * @return void
 */
function logout()
{
    $redirectUrl = \IPS\Http\Url::internal('');
    $member = \IPS\Member::loggedIn();

    /* Are we logging out back to an admin user? */
    if (isset($_SESSION['logged_in_as_key'])) {
        $key = $_SESSION['logged_in_as_key'];
        unset(\IPS\Data\Store::i()->$key);
        unset($_SESSION['logged_in_as_key']);
        unset($_SESSION['logged_in_from']);

        return;
    }

    \IPS\Request::i()->setCookie('member_id', null);
    \IPS\Request::i()->setCookie('pass_hash', null);
    \IPS\Request::i()->setCookie('anon_login', null);

    foreach (\IPS\Request::i()->cookie as $name => $value) {
        if (mb_strpos($name, "ipbforumpass_") !== false) {
            \IPS\Request::i()->setCookie($name, null);
        }
    }

    session_destroy();

    /* Login handler callback */
    foreach (\IPS\Login::handlers(true) as $k => $handler) {
        try {
            $handler->logoutAccount($member, $redirectUrl);
        } catch (\BadMethodCallException $e) {

		}
    }

    /* Member sync callback */
    $member->memberSync('onLogout', array($redirectUrl));
}
