<template>

  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @click.prevent="readNotification()">
        Notification <span class="badge badge-primary">{{notification_total}}</span>
        <span class="caret"></span>
    </a>
    <div v-if="notifications.length" style="width:250px;max-height:500px;overflow:auto" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <div  class="m-1 p-1 border border-success rounded" v-for="notification in notifications">
            <div><strong>{{notification.message}}</strong></div>
            <small class="float-left text-primary">{{notification.date_time | humanReadableTime}}</small>

            <small class="float-right text-primary">{{notification.date_time }}</small>
            <div style="clear:both"></div>
        </div>
    </div>

    <div v-else style="width:250px;max-height:500px;overflow:auto" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <div class="m-1 p-1 border border-success rounded">
              You don't have any unread notifications.
        </div>
    </div>
</li>
</template>

<script>
 import axios from 'axios';

    export default {
        data: () => ({
            loading: false,
            notifications:[],
            notification_total:0,
            todayDate: ''
        }),
        mounted() {
            this.listen();
            this.getNotification();
        },
        methods: {
            getNotification () {
              this.loading = true

              axios.get('/get-notification')
                .catch(error => console.log(error))
                .then((response) => { 

                    if(typeof response.data!=undefined)
                    {
                        this.notifications = response.data;
                        this.notification_total=Object.keys(response.data).length;
                        var res=[];
                        var _this=this;
                        Object.keys(this.notifications)
                      .map(function(k) {
                        res.push({
                            message:_this.notifications[k]['message'],
                            // created_at:_this.notifications[k]['created_at'],
                            date_time:_this.notifications[k]['date_time'],
                            });
                      });
                      
                      _this.notifications=res;                        
                    }
                    else
                        this.notifications=[];
                    this.loading = false
                })
            },
            listen () {
                var _this=this;
                window.Echo.private(`message-notification.${window.Laravel.user.id}`)
                .listen('MessageEvent', (response) => {
                    // this.getNotification();
                    var res=[];
                    res.push({
                            message:response.data.message,
                            // created_at:response.data.created_at,
                            date_time:response.data.date_time,
                        });
                    
                    Object.keys(this.notifications)
                      .map(function(k) {
                        res.push({
                            message:_this.notifications[k]['message'],
                            // created_at:_this.notifications[k]['created_at'],
                            date_time:_this.notifications[k]['date_time'],
                            });
                      });
                      
                      _this.notifications=res;
                      _this.notification_total=res.length;

                    // console.log(response.data);
                    // _this.notifications.unshift(response.data);

                    if (! ('Notification' in window)) {
                      alert('Web Notification is not supported');
                      return;
                    }

                    Notification.requestPermission( permission => {
                      let notification = new Notification('New Notification', {
                        body: response.data.message, // content for the alert
                        icon: "https://pusher.com/static_logos/320x320.png" // optional image url
                      });

                      // link to page on clicking the notification
                      // notification.onclick = () => {
                      //   window.open(window.location.href);
                      // };
                    });

                });

                // public channel
                // window.Echo.channel('message-channel')
                // .listen('MessageEvent', (e) => {
                //     console.log("event",e);
                // });
            },
            readNotification () {
                axios.post('/read-notification')
                    .catch(error => console.log(error))
                    .then((response) => { 
                        this.notification_total=0;
                    });
            }
        }
        
    }
</script>
