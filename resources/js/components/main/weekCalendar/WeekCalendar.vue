<template>
    <div>
        <!-- Class Timetable Section Begin -->
        <section class="class-timetable-section spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <span>Weekly Program</span>
                            <h2>FIND YOUR TIME</h2>
                        </div>
                    </div>
<!--                    <div class="col-lg-6">-->
<!--                        <div class="table-controls">-->
<!--                            <ul>-->
<!--                                <li class="active" data-tsfilter="all">All event</li>-->
<!--                                <li data-tsfilter="fitness">Fitness tips</li>-->
<!--                                <li data-tsfilter="motivation">Motivation</li>-->
<!--                                <li data-tsfilter="workout">Workout</li>-->
<!--                            </ul>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="class-timetable">
                            <table>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                    <th>Saturday</th>
                                    <th>Sunday</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(weekDays, index1) in weekDaysPerTime">
                                    <td class="class-time">{{weekDays.start_time}}</td>
                                    <td v-for="(gymClasses, index2) in weekDays.days" class="ts-meta" :class="[{'dark-bg':(index1%2 === 0 && index2%2 === 0) || (index1%2 !== 0 && index2%2 !== 0)}]">
                                        <div class="class-height" :class="{'hover-bg': gymClasses.length === 1}">
                                            <div v-for="gymClass in gymClasses" :class="{'hover-bg': gymClasses.length > 1}">
                                                <h5>{{gymClass.gym_class_name}}</h5>
                                                <span>{{gymClass.teacher}}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Class Timetable Section End -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                weekDaysPerTime: {},
                darkBackgroundRow: true,
            }
        },
        mounted() {
            axios.get('/calendar/week', this.form)
                .then((results) => {

                    console.log(results.data);

                    // var arr1 = ['left', 'top'];
                    // const arr2 = arr1.map(value => ({[value]: 0}));
                    //
                    this.weekDaysPerTime = results.data;

                    // for (const [time, value] of Object.entries(results.data)) {
                        // console.log(results.data[time]);

                    //
                    //     // this.weekDays.push(value);
                    //
                    //     // this.weekDays = arr1.map(value => ({[value]: 0}));
                    //
                    //
                    //     for (const [day, value2] of Object.entries(value)) {
                    //
                    //         results.data[time][day].darkBackgroundRow = darkBackgroundRow;
                    //         darkBackgroundRow = !darkBackgroundRow;
                    //         this.weekDays[time][day] = [];
                    //
                    //         for (const [index, gymClass] of Object.entries(value2)) {
                    //             this.weekDays[time][day].push(gymClass);
                    //
                    //         }
                    //     }
                    //     // this.weekDays[time] = value;
                    //
                    //
                    //     // if ((count+1)%7 === 0) {
                    //     //     this.changeLineCalendarDateIndexes.push(count);
                    //     // }
                    //     //
                    //     // ++count;
                    // }

                    // results.data.data.forEach((value, index) => {
                    //     this.weekDays.push(value);
                    // });

                    console.log(this.weekDaysPerTime);
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{

        }
    }
</script>
