<template>
    <div>
        <form method="POST" action="/sales/settings/set-month-goal">
            <h4>Load Video for Deposits</h4>
            <hr>

            <div class="form-group">
                <label for="small_deposit">Small deposits video (Up to $10,000):</label>
                <input @change="loadVideo" type="file" name="small_deposit" id="small_deposit" title="Small deposit">
            </div>

            <div class="form-group">
                <label for="big_deposit">Big deposits video (More then $10,000):</label>
                <input @change="loadVideo" type="file" name="big_deposit" id="big_deposit" title="Big deposit">
            </div>

            <div class="form-group">
                <button @click.prevent="updateVideo" class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i> Load Video</button>
            </div>
        </form>
    </div>
</template>

<script type="text/babel">
    import Clock from './Clock.vue';
    import WinnersBoard from './WinnersBoard.vue';
    import Stats from './Stats.vue';
    import Pusher from 'pusher-js';

    export default {
        name: 'winner-video',
        data(){
            return {
                small_video: {},
                big_video: {},
                src: ""
            }
        },
        components: {
            Clock,
            WinnersBoard,
            Stats
        },

        methods: {
            loadVideo(event){
                let input = event.target;

                if(input.files && input.files[0]){
                    let reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);

                    if(input.name == "small_deposit")
                        this.small_video = input.files[0];
                    else
                        this.big_video = input.files[0];
                    
                }
            },

            updateVideo(){
                let form = new FormData();
                form.append('small_video', this.small_video);
                form.append('big_video', this.big_video);
                this.$http.post('/screens/add-winner-video', form)
                    .then(response => {
                        swal("Done", "Your video has been uploaded successfully.", "success");
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    })
                    .catch(errors => {
                        let error = Object.values(errors.body)[0][0];
                        swal("Oops...", error, "error");
                    });
            }
        }
    }
</script>


