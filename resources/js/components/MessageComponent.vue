<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Message Component</div>

                    <div class="card-body">
                        <form id="formMessage">
                          <div class="form-group">
                            <label for="to_user">Select User:</label>
                            <select name="to_user" id="to_user" class="form-control">
                                <option value="">Select User</option>
                                <option v-for="(user,id) in users" v-bind:value="id">{{user}}</option>
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" id="message" name="message">
                            </textarea>
                          </div>
                          <button type="button" class="btn btn-default" :disabled="loading" @click="sendMessage">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data: () => ({
            loading: false,
            users:[],
        }),
        mounted() {
            this.getUsers();
        },
        methods: {
            sendMessage () {
              this.loading = true

              axios.post('/send-message',$('#formMessage').serialize())
                .catch(error => console.log(error))
                .then((response) => {
                    console.log(response);
                    this.loading = false
                 })
            },
            getUsers () {
              this.loading = true

              axios.get('/get-users')
                .catch(error => console.log(error))
                .then((response) => { 
                    if(typeof response.data.users!=undefined)
                        this.users=response.data.users;
                    else
                        this.users=[];

                    this.loading = false
                })
            }
        },
    }
</script>
