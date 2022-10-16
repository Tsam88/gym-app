<template>
        <div>
            <!--response message alert-->
            <b-alert :show="$alertHandler.dismissCountDown" dismissible fade :variant="$alertHandler.variant" @dismissed="$alertHandler.dismissCountDown=0" @dismiss-count-down="$alertHandler.countDownChanged" class="message-alert">
                {{$alertHandler.responseMessage}}
            </b-alert>

            <router-view class="view"></router-view>
        </div>

</template>

<script>
    export default {

    }
</script>
