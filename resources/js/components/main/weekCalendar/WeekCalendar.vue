<template>
    <div>
        <!-- Class Timetable Section Begin -->
        <section id="weekly-program" class="class-timetable-section spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <span>Weekly Program</span>
                            <h2>FIND YOUR TIME</h2>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="table-controls mb-3">
                            <ul class="p-1">
                                <li class="active" data-tsfilter="all">All event</li>
                                <li v-for="gymClassName in gymClassNames" :data-tsfilter="gymClassName">{{gymClassName}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="class-timetable-overflow-x flipped">
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
                                        <th class="no-border-right">Sunday</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(weekDays, index1) in weekDaysPerTime">
                                        <td class="class-time" :class="{'no-border-bottom':index1 === Object.keys(weekDaysPerTime).length-1}">{{weekDays.start_time}}</td>
                                        <td v-for="(gymClasses, index2) in weekDays.days" :class="[{'dark-bg':(index1%2 === 0 && index2%2 === 0) || (index1%2 !== 0 && index2%2 !== 0)}]">
                                            <div class="class-height" :class="{'hover-bg': gymClasses.length === 1}">
                                                <div v-for="gymClass in gymClasses" class="ts-meta" :data-tsmeta="gymClass.gym_class_name" :class="{'hover-bg': gymClasses.length > 1}">
<!--                                                    <h5 :style="{color: gymClass.color}">{{gymClass.gym_class_name}}</h5>-->
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
                gymClassNames: [],
            }
        },
        mounted() {
            axios.get('/calendar/week', this.form)
                .then((results) => {
                    this.weekDaysPerTime = results.data['week_calendar'];
                    this.gymClassNames = results.data['gym_class_names'];
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
    }
</script>
