<template>
    <div>

        <h1>Home</h1>

        <drop-down-login></drop-down-login>

        <router-view class="view"></router-view>


    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component Home mounted.')
        }
    }
</script>
