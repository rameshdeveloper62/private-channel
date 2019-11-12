(1) first of all we should create table for store notification
	php artisan notifications:table
	php artisan migrate

(2) we create create sendMessage notification class
	App\Notifications\SendMessage.php
	-> php artisan make:notification SendMessage
	-> we used datatable type of notification 

		public function via($notifiable)
	    {
	        return ['database'];
	    }
	-> toArray() method for store data in notification table
	    public function toArray($notifiable)
	    {
	        return $this->data;
	    }

(3) we used by default user reference table, Notifiable trait is already defined in user model 
(4) we send notification when user add new message
	
	$to_user->notify(new SendMessage($notification));
	
	we send notification when user add new message at that time listen event
	
	broadcast(new MessageEvent($data))->toOthers();

(5) we can display current login user notification by $user->notifications.
	if we want to display only read,unread,all but we can able to display all type of notification

	-> read and unread = $user->notifications
	-> unread = $user->unreadNotifications
	-> Marking Notifications As Read

		$user = App\User::find(1);

		foreach ($user->unreadNotifications as $notification) {
		    $notification->markAsRead();
		}
		// all notification read by current user object
		$user->unreadNotifications->markAsRead();
		
		// mark as read by update method
		$user->unreadNotifications()->update(['read_at' => now()]);

	-> delete notification
		$user->notifications()->delete();
(6) we can change in config/app.php file for enable brodcast service provider
	uncomment this line :  App\Providers\BroadcastServiceProvider::class,

(7) change of BROADCAST_DRIVER=log to pusher in .env file
	BROADCAST_DRIVER=pusher

(8) add pusher key,secret,cluster in .env file

(9) create event for send notification 
	if you didn't add event name and listener so these command are run. it will automatically add in app/Providers/EventServiceprovider.php.

	php artisan make:event MessageEvent

	define channel name

	public function broadcastOn()
    {
        return ['message-notification'];
    }

(10) when user send message to other user.
	HomeController@store method
	event(new MessageEvent($notification));

(11) when current login user send message it will automatically display in top bar 	notification bar

	In home.blade.php

	  //instantiate a Pusher object with our Credential's key
      var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
          cluster: '{{env('PUSHER_APP_CLUSTER')}}',
          encrypted: true
      });

      //Subscribe to the channel we specified in our Laravel Event
      var channel = pusher.subscribe('my-channel');

      //Bind a function to a Event (the full Laravel class)
      channel.bind('App\\Events\\MessageEvent', addMessage);

      function addMessage(data) {
        console.log(data);
      }

(12) In app.js 
	
	add require packages and import component

(13) NotificationComponent.vue
	
	In this component getNotification() for get latest 10 notification
	In this component listen() for get listen event and display new notification
		
		"message-notification.user_id" is private channel name
		"MessageEvent" is listen for new notification

		window.Echo.private(`message-notification.${window.Laravel.user.id}`)
           .listen('MessageEvent', (response) => {
        })

 (14) MessageComponent.vue
	
	In this component getUsers() method for display list of users, you can choose from dropdown for you will send notification selected user

	In this component sendMessage() method for send notification to server

	Enjoying........
