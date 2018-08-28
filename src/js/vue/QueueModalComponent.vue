<!--
  - Copyright (C) 2018 Michael Milawski - All Rights Reserved
  - You may use, distribute and modify this code under the
  -  terms of the MIT license.
  -->

<template>
<div>

    <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <p>One fine body&hellip;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>
</template>

<script>
    export default {
        data () {
            return {
                job: {},
            }
        },
        methods: {
            },

        mounted() {

            this.$root.$on("job_modal_open", function(data){
                //th.loadJobs();
                console.log("Hello from job modal!");
            });
        }
    }
</script>