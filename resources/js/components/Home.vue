<template>
    <div>

        <h1>Home</h1>



        <div>
            <drop-down-login class="float-right"></drop-down-login>

            <div class="float-right">
                <router-link to="/calendar">
                    <b-dropdown-item-button>Calendar</b-dropdown-item-button>
                </router-link>
            </div>

        </div>


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
