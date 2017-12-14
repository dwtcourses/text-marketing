<?php

namespace App\Http\Controllers;

use App\User;
use App\Team;
use App\Plan;
use App\Link;
use App\Homeadvisor;
use App\Http\Requests\SignUpRequest;
use App\Events\SignUp;
use App\Mail\Support;
use App\Mail\Recovery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public $salt = 'eEZue4JfUvJJKn9N';

    public function info()
    {
        return auth()->user();
    }
    
    public function signin(SignUpRequest $request)
    {
        if (auth()->validate($request->all())) {
            auth()->attempt($request->all());
            return $this->message('You are in', 'success');
        }

        return $this->message('Invalid Email or Password');
    }

    public function signup($id = false, $post = [])
    {
        $validator = $this->validate(request(), [
            'email' => 'required|email|unique:users,email',
            'firstname' => 'required',
            'password' => 'required',
            'ha_rep' => 'required_if:plans_code,home-advisor'
        ]);

        if ( ! $validator->fails()) {
            $team = new Team();
            $team->name = $this->teamsName($post);
            $team->save();

            $plan = $this->getPlan($post['plans_id']);
            $trial = $plan ? $plan->trial : 0;

            $user = new User();
            $user->password = bcrypt($post['password']);
            $user->plans_id = $post['plans_id'].'-'.strtolower(config('app.name'));
            $user->teams_id = $team->id;
            $user->teams_leader = 1;
            $user->type = 2;
            $user->email = strtolower($post['email']);
            $user->firstname = $post['firstname'];
            $user->lastname = ! empty($post['lastname']) ? $post['lastname'] : '';
            $user->active = 1;
            $user->trial_ends_at = Carbon::now()->addDays($trial);
            $user->save();

            auth()->login($user);
            $owner = User::where('owner', 1)->first();
            event(new SignUp($user, $owner));

            if ( ! empty($post['ha_rep'])) {
                $this->createHa($user, $post);
            }

            return $this->message(__("You were successfully registered."), 'success');
        }
        return false;
    }

    public function createHa($user, $post)
    {
        $homeadvisor = Homeadvisor::firstOrNew(['users_id' => $user->id]);
        $homeadvisor->text = '';
        $homeadvisor->rep = $post['ha_rep'];
        $homeadvisor->save();
    }

    public function getPlan($plans_id)
    {
        return Plan::where('plans_id', $plans_id.'-'.strtolower(config('app.name')))->first();
    }

    public function createSubscriptions($user)
    {
        /*$user->newSubscription('main', 'home-advisor-contractortexter')->create([
            'email' => $user->email,
            'trial_ends_at' => Carbon::now()->addDays(14),
        ]);
        $user = User::create([
            'trial_ends_at' => ,
        ]);*/
    }

    public function teamsName($post)
    {
        $name = [$post['firstname']];

        if ( ! empty($post['lastname'])) {
           $name[] = $post['lastname'];
        }
        return implode(' ', $name);
    }

    public function signout()
    {
        $user = auth()->user();
        if ( ! empty($user->admins_id)) {
            $admin = User::find($user->admins_id);
            auth()->login($admin);
            
            $user->admins_id = 0;
            $user->save();
        } else {
            auth()->logout();
        }

        return $this->message('You are out', 'success');
    }

    public function support($id = false, $post = [])
    {
        $owner = User::where('owner', 1)->first();
        Mail::to($owner)->send(new Support($post));
        return $this->message(__("Your email successfully sent."), 'success');
    }

    public function recovery($id = false, $post = [])
    {
        $user = User::where('email', strtolower($post['email']))->first();
        if ( ! empty($user)) {
            $password = crypt($user->password, time());
            $user->password = bcrypt($password);
            $user->save();

            Mail::to($post['email'])->send(new Recovery(['pass' => $password, 'email' => $post['email']]));
            return $this->message(__("New password was sent to your email address."), 'success');
        }
        return $this->message(__("Invalid email."));
    }
}
