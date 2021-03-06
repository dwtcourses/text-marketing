<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Dialog;
use App\Text;
use App\Receiver;
use App\ContactList;
use App\Http\Requests\MessageCreateRequest;
use Carbon\Carbon;
use App\Jobs\SendMarketingText;
use App\Libraries\ApiValidate;
use App\Http\Services\MessagesService;
use Illuminate\Support\Facades\Storage;

class MessagesController extends Controller
{
	public function info($id = false)
	{
		return Message::find($id);
	}

    public function all()
    {
        return auth()->user()->messages()->orderBy('created_at', 'desc')->with(['texts.receivers'])->get()->toArray();
    } 

    public function create(MessageCreateRequest $request)
    {
        if ($this->textValidate($request)) {
            $data = $request->only(['lists_id', 'text', 'file', 'schedule', 'switch', 'x_day']);

            $data['lists_id'] = implode(',', $data['lists_id']);
            $data['date'] = $this->getDate($request->schedule, $request->time, auth()->user());
            $data['finish_date'] = $this->getFinishDate($request->finish_date, auth()->user());
            $data['token'] = $data['date'];
            $data['active'] = true;
            $data['file'] = ! empty($data['file']) ? $data['file'] : '';
            $message = auth()->user()->messages()->create($data);
            
            if ( ! empty($data['file'])) {
                $temp = explode('.', $data['file']);
                $name = auth()->user()->id.'.'.$temp[1];
                Storage::move(str_replace('storage', 'public', $data['file']), 'public/upload/marketing/'.$message->id.'/'.auth()->user()->id.'/'.$name);
                $file = 'storage/upload/marketing/'.$message->id.'/'.auth()->user()->id.'/'.$name;

                $message->update(['file' => $file]);
            }

            $this->sendText($message);
            return $this->message('Message was successfully saved', 'success');
        }

        return false;
    }

    public function update(MessageCreateRequest $request, $id = false)
    {
        if ($this->textValidate($request)) {
            $data = $request->only(['lists_id', 'text', 'file', 'schedule', 'switch', 'x_day']);
            $file = '';
            
            if ( ! empty($data['file'])) {
                $temp = explode('.', $data['file']);
                $name = auth()->user()->id.'.'.$temp[1];
                if (strpos($data['file'], 'temp') !== false) {
                    Storage::deleteDirectory('public/upload/marketing/'.$id.'/'.auth()->user()->id);
                    Storage::copy(str_replace('storage', 'public', $data['file']), 'public/upload/marketing/'.$id.'/'.auth()->user()->id.'/'.$name);
                }
                $file = 'storage/upload/marketing/'.$id.'/'.auth()->user()->id.'/'.$name;
            } else {
                Storage::deleteDirectory('public/upload/marketing/'.$id.'/'.auth()->user()->id);
            }

            $data['lists_id'] = implode(',', $data['lists_id']);
            $data['date'] = $this->getDate($request->schedule, $request->time, auth()->user());
            $data['finish_date'] = $this->getFinishDate($request->finish_date, auth()->user());
            $data['token'] = $data['date'];
            $data['active'] = true;
            $data['file'] = $file;
            
            $message = Message::find($id);
            $message->update($data);
            $this->sendText($message);
            return $this->message('Message was successfully saved', 'success');
        }

        return false;
    }

    public function sendText($message)
    {
        $clients = $this->sendClients($message->lists_id);

        $text = MessagesService::createText($message, $clients);

        $phones = [];

        foreach ($clients as $client) {
            $row = [
                'phone' => $client->phone,
            ];

            if (strpos($message->text, '[$FirstName]') !== false) {
                $row['firstname'] = $client->firstname;
            }

            if (strpos($message->text, '[$LastName]') !== false) {
                $row['lastname'] = $client->lastname;
            }

            $phones[] = $row;

            MessagesService::createReceiver($text, $client);
        }
        
        $delay = Carbon::now()->diffInSeconds($message->date);
        SendMarketingText::dispatch($text, $phones, $message, auth()->user()->company_name, $message->token->toDateTimeString())->onQueue('texts')->delay($delay);
    }

    public function sendClients($list_ids)
    {
        $result = [];
        $exists = [];
        $list_ids = explode(',', $list_ids);
        $lists = ContactList::whereIn('id', $list_ids)->with('clients')->get();

        foreach ($lists as $list) {
            foreach ($list->clients as $client) {
                if ( ! in_array($client->phone, $exists)) {
                    $result[] = $client;
                    $exists[] = $client->phone;
                }
            }
        }

        return $result;
    }

    public function remove($id = false)
    {
        Message::destroy($id);
        return $this->message(__('Message was successfully removed'), 'success');
    }

    public function getDate($schedule, $time, $user, $validate = false)
    {
        $date = Carbon::now()->subHours($user->offset);
        if ( ! empty($schedule)) {
            $date = Carbon::create($time['year'], $time['month'], $time['date'], $time['hours'], $time['minutes'], 0, config('app.timezone'));
        }

        if (empty($validate)) {
            $date->addHours($user->offset);
        }

        return $date;
    }

    public function getFinishDate($time, $user)
    {
        $date = Carbon::create($time['year'], $time['month'], $time['date'], $time['hours'], $time['minutes'], 0, config('app.timezone'));
        $date->addHours($user->offset);
        return $date;
    }

    public function textValidate(Request $request)
    {
        $data = $request->all(); 
        $date = '';
        if ( ! empty($data['schedule'])) {
            $time = $data['time'];
            $date = Carbon::create($time['year'], $time['month'], $time['date'], $time['hours'], $time['minutes'], 0, config('app.timezone'));
        }

        if ( ! ApiValidate::companyExists(auth()->user()->company_name, auth()->user())) {
            return $this->message('This Company Name isn\'t verified');
        }

        if ( ! ApiValidate::companyVerified(auth()->user()->company_name, auth()->user())) {
            return $this->message('Company Name must be verified');
        }

        $text = trim($data['text']);
        if ( ! ApiValidate::messageSymbols($text)) {
            return $this->message('SMS Text contains forbidden characters');
        }

        if ( ! empty($data['clients'])) {
            $length = true;
            $phones = true;
            $limit = true;
            foreach ($data['clients'] as $client) {
                $message = $text;

                if ( ! empty($client['firstname'])) {
                    $message = str_replace('[$FirstName]', $client['firstname'], $message);
                }

                if ( ! empty($client['lastname'])) {
                    $message = str_replace('[$LastName]', $client['lastname'], $message);
                }

                if ( ! ApiValidate::messageLength($message, $data['company'])) {
                    $length = false;
                }

                if ( ! ApiValidate::phoneFormat($client['phone'])) {
                    $phones = false;
                }
                
                if (ApiValidate::underLimitMarketing($client['id'], $date)) {
                    $limit = false;
                }
            }

            if (empty($length)) {
                return $this->message('SMS Text is too long. Text will not be send');
            }

            if (empty($phones)) {
                return $this->message('Some client\'s phone numbers have wrong format. Text will not be send');
            }

            if (empty($limit)) {
                $this->message('Some client\'s phone numbers already received texts during last 24h. Text will not be send');
            }
        }

        if (ApiValidate::underBlocking($this->getDate($request->schedule, $request->time, auth()->user(), true))) {
            return $this->message('You can\'t send texts before 9 AM. You can try to use Schedule Send');
        }

        return 1;
    }

    public function changeActive(Request $request, Message $message)
    {
        $data = $request->only('active');
        $message->update($data);
    }

    public function push(Request $request, Text $text)
    {
        $data = $request->json()->all();
        $clients = [];
        foreach ($data as $client) {
            $clients[$client['phone']] = $client;
        }

        MessagesService::receivers($text, $clients);
    }

    public function inbox(Request $request, Message $message)
    {
        $data = $request->only(['CONTENTS', 'PHONENUMBER']);

        $dialog = [
            'users_id' => $message->user_id,
            'clients_id' => $this->clientId($message->lists_id, $data['PHONENUMBER']),
            'text' =>  $data['CONTENTS'],
            'new' => true,
            'status' => 1,
            'my' => false
        ];
        
        Dialog::create($dialog);
    }

    public function clientId($list_ids, $phone)
    {
        $list_ids = explode(',', $list_ids);
        $lists = ContactList::whereIn('id', $list_ids)->with('clients')->get();

        foreach ($lists as $list) {
            foreach ($list->clients as $client) {
                if ($client->phone == $phone) {
                    return $client->id;
                }
            }
        }
    }
}
