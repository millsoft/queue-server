<template>
<div>


    <table v-if="jobs.length" class="table table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Priority</th>
                <th>Context</th>
                <th>Output</th>
                <th>Added</th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="job in jobs">
                <td>{{job.id}}</td>
                <td>{{job.priority}}</td>
                <td>{{job.context}}</td>
                <td>{{job.output}}</td>
                <td>{{job.time_added}}</td>
            </tr>
        </tbody>
    </table>
    <div v-else>
        No Jobs
    </div>


</div>
</template>

<script>
    export default {
        props: ["jobid", "limit"],
        data () {
            return {
                jobs: [],
                config: {
                    //How many items should be displayed?
                    limit: 2,

                    //Get new data every x seconds
                    autoupdate: 0
                }

            }
        },
        methods: {
            loadJobs: function(){
                var th = this;
                var params = {
                    id: this.jobid,
                    limit: this.config.limit,
                };

                $.post('jobs/get', params , function(d){
                    th.jobs = d.data;
                }, 'json');
            },

            //Auto reload for data
            autoUpdate(){
                if(this.config.autoupdate > 0){
                    setInterval(this.loadJobs, this.config.autoupdate * 1000);
                }
            },

            loadConfig(){
                this.config.limit = this.limit || this.config.limit;
                this.config.autoupdate = this.autoupdate || this.config.autoupdate;
            }

            },

        mounted() {
            //Load the configuration from props
            this.loadConfig();

            //Load the jobs via ajax
            this.loadJobs();

            //Start the autoupdater
            this.autoUpdate();
        }
    }
</script>