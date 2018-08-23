<template>
<div>
    <h1>hehehehe</h1>
</div>
</template>

<script>
    export default {
        data () {
            return {
                base: $("#base").val(),
                csrf: $("#csrf").val(),
            }
        },
        methods: {

        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
