<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Δημιουργία Τμήματος</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form @submit.prevent="submitForm">
                            <div class="form-group">

                                <div class="mb-3">
                                    <label for="name">Όνομα</label>
                                    <input v-model="form.name" id="name" name="name" type="text" class="form-control" placeholder="Όνομα" required>
                                </div>

                                <div class="my-3">
                                    <label for="description">Περιγραφή</label>
                                    <input v-model="form.description" id="description" name="description" type="text" class="form-control" placeholder="Περιγραφή">
                                </div>

                                <div class="my-3">
                                    <label for="teacher">Δάσκαλος</label>
                                    <input v-model="form.teacher" id="teacher" name="teacher" type="text" class="form-control" placeholder="Δάσκαλος">
                                </div>

                                <div class="my-3">
                                    <label for="number_of_months">Αριθμός μαθητών</label>
                                    <input v-model="form.number_of_students" id="number_of_months" name="number_of_months" type="number" min="1" step="1" class="form-control" placeholder="Αριθμός μαθητών" required>
                                </div>

<!--                                <div class="my-3">-->
<!--                                    <label for="color">Color</label>-->
<!--                                    <select v-model="form.color" id="color" name="color" class="form-select mb-3" required>-->
<!--                                        <option value=AliceBlue" style="color:AliceBlue;">AliceBlue</option>-->
<!--                                        <option value="AntiqueWhite" style="color:AntiqueWhite;">AntiqueWhite</option>-->
<!--                                        <option value="Aqua" style="color:Aqua;">Aqua</option>-->
<!--                                        <option value="Aquamarine" style="color:Aquamarine;">Aquamarine</option>-->
<!--                                        <option value="Azure" style="color:Azure;">Azure</option>-->
<!--                                        <option value="Beige" style="color:Beige;">Beige</option>-->
<!--                                        <option value="Bisque" style="color:Bisque;">Bisque</option>-->
<!--                                        <option value="Black" style="color:Black;">Black</option>-->
<!--                                        <option value="BlanchedAlmond" style="color:BlanchedAlmond;">BlanchedAlmond</option>-->
<!--                                        <option value="Blue" style="color:Blue;">Blue</option>-->
<!--                                        <option value="BlueViolet" style="color:BlueViolet;">BlueViolet</option>-->
<!--                                        <option value="Brown" style="color:Brown;">Brown</option>-->
<!--                                        <option value="BurlyWood" style="color:BurlyWood;">BurlyWood</option>-->
<!--                                        <option value="CadetBlue" style="color:CadetBlue;">CadetBlue</option>-->
<!--                                        <option value="Chartreuse" style="color:Chartreuse;">Chartreuse</option>-->
<!--                                        <option value="Chocolate" style="color:Chocolate;">Chocolate</option>-->
<!--                                        <option value="Coral" style="color:Coral;">Coral</option>-->
<!--                                        <option value="CornflowerBlue" style="color:CornflowerBlue;">CornflowerBlue</option>-->
<!--                                        <option value="Cornsilk" style="color:Cornsilk;">Cornsilk</option>-->
<!--                                        <option value="Crimson" style="color:Crimson;">Crimson</option>-->
<!--                                        <option value="Cyan" style="color:Cyan;">Cyan</option>-->
<!--                                        <option value="DarkBlue" style="color:DarkBlue;">DarkBlue</option>-->
<!--                                        <option value="DarkCyan" style="color:DarkCyan;">DarkCyan</option>-->
<!--                                        <option value="DarkGoldenRod" style="color:DarkGoldenRod;">DarkGoldenRod</option>-->
<!--                                        <option value="DarkGray" style="color:DarkGray;">DarkGray</option>-->
<!--                                        <option value="DarkGreen" style="color:DarkGreen;">DarkGreen</option>-->
<!--                                        <option value="DarkKhaki" style="color:DarkKhaki;">DarkKhaki</option>-->
<!--                                        <option value="DarkMagenta" style="color:DarkMagenta;">DarkMagenta</option>-->
<!--                                        <option value="DarkOliveGreen" style="color:DarkOliveGreen;">DarkOliveGreen</option>-->
<!--                                        <option value="Darkorange" style="color:Darkorange;">Darkorange</option>-->
<!--                                        <option value="DarkOrchid" style="color:DarkOrchid;">DarkOrchid</option>-->
<!--                                        <option value="DarkRed" style="color:DarkRed;">DarkRed</option>-->
<!--                                        <option value="DarkSalmon" style="color:DarkSalmon;">DarkSalmon</option>-->
<!--                                        <option value="DarkSeaGreen" style="color:DarkSeaGreen;">DarkSeaGreen</option>-->
<!--                                        <option value="DarkSlateBlue" style="color:DarkSlateBlue;">DarkSlateBlue</option>-->
<!--                                        <option value="DarkSlateGray" style="color:DarkSlateGray;">DarkSlateGray</option>-->
<!--                                        <option value="DarkTurquoise" style="color:DarkTurquoise;">DarkTurquoise</option>-->
<!--                                        <option value="DarkViolet" style="color:DarkViolet;">DarkViolet</option>-->
<!--                                        <option value="DeepPink" style="color:DeepPink;">DeepPink</option>-->
<!--                                        <option value="DeepSkyBlue" style="color:DeepSkyBlue;">DeepSkyBlue</option>-->
<!--                                        <option value="DimGray" style="color:DimGray;">DimGray</option>-->
<!--                                        <option value="DodgerBlue" style="color:DodgerBlue;">DodgerBlue</option>-->
<!--                                        <option value="FireBrick" style="color:FireBrick;">FireBrick</option>-->
<!--                                        <option value="FloralWhite" style="color:FloralWhite;">FloralWhite</option>-->
<!--                                        <option value="ForestGreen" style="color:ForestGreen;">ForestGreen</option>-->
<!--                                        <option value="Fuchsia" style="color:Fuchsia;">Fuchsia</option>-->
<!--                                        <option value="Gainsboro" style="color:Gainsboro;">Gainsboro</option>-->
<!--                                        <option value="GhostWhite" style="color:GhostWhite;">GhostWhite</option>-->
<!--                                        <option value="Gold" style="color:Gold;">Gold</option>-->
<!--                                        <option value="GoldenRod" style="color:GoldenRod;">GoldenRod</option>-->
<!--                                        <option value="Gray" style="color:Gray;">Gray</option>-->
<!--                                        <option value="Green" style="color:Green;">Green</option>-->
<!--                                        <option value="GreenYellow" style="color:GreenYellow;">GreenYellow</option>-->
<!--                                        <option value="HoneyDew" style="color:HoneyDew;">HoneyDew</option>-->
<!--                                        <option value="HotPink" style="color:HotPink;">HotPink</option>-->
<!--                                        <option value="IndianRed" style="color:IndianRed;">IndianRed</option>-->
<!--                                        <option value="Indigo" style="color:Indigo;">Indigo</option>-->
<!--                                        <option value="Ivory" style="color:Ivory;">Ivory</option>-->
<!--                                        <option value="Khaki" style="color:Khaki;">Khaki</option>-->
<!--                                        <option value="Lavender" style="color:Lavender;">Lavender</option>-->
<!--                                        <option value="LavenderBlush" style="color:LavenderBlush;">LavenderBlush</option>-->
<!--                                        <option value="LawnGreen" style="color:LawnGreen;">LawnGreen</option>-->
<!--                                        <option value="LemonChiffon" style="color:LemonChiffon;">LemonChiffon</option>-->
<!--                                        <option value="LightBlue" style="color:LightBlue;">LightBlue</option>-->
<!--                                        <option value="LightCoral" style="color:LightCoral;">LightCoral</option>-->
<!--                                        <option value="LightCyan" style="color:LightCyan;">LightCyan</option>-->
<!--                                        <option value="LightGoldenRodYellow" style="color:LightGoldenRodYellow;">LightGoldenRodYellow</option>-->
<!--                                        <option value="LightGrey" style="color:LightGrey;">LightGrey</option>-->
<!--                                        <option value="LightGreen" style="color:LightGreen;">LightGreen</option>-->
<!--                                        <option value="LightPink" style="color:LightPink;">LightPink</option>-->
<!--                                        <option value="LightSalmon" style="color:LightSalmon;">LightSalmon</option>-->
<!--                                        <option value="LightSeaGreen" style="color:LightSeaGreen;">LightSeaGreen</option>-->
<!--                                        <option value="LightSkyBlue" style="color:LightSkyBlue;">LightSkyBlue</option>-->
<!--                                        <option value="LightSlateGray" style="color:LightSlateGray;">LightSlateGray</option>-->
<!--                                        <option value="LightSteelBlue" style="color:LightSteelBlue;">LightSteelBlue</option>-->
<!--                                        <option value="LightYellow" style="color:LightYellow;">LightYellow</option>-->
<!--                                        <option value="Lime" style="color:Lime;">Lime</option>-->
<!--                                        <option value="LimeGreen" style="color:LimeGreen;">LimeGreen</option>-->
<!--                                        <option value="Linen" style="color:Linen;">Linen</option>-->
<!--                                        <option value="Magenta" style="color:Magenta;">Magenta</option>-->
<!--                                        <option value="Maroon" style="color:Maroon;">Maroon</option>-->
<!--                                        <option value="MediumAquaMarine" style="color:MediumAquaMarine;">MediumAquaMarine</option>-->
<!--                                        <option value="MediumBlue" style="color:MediumBlue;">MediumBlue</option>-->
<!--                                        <option value="MediumOrchid" style="color:MediumOrchid;">MediumOrchid</option>-->
<!--                                        <option value="MediumPurple" style="color:MediumPurple;">MediumPurple</option>-->
<!--                                        <option value="MediumSeaGreen" style="color:MediumSeaGreen;">MediumSeaGreen</option>-->
<!--                                        <option value="MediumSlateBlue" style="color:MediumSlateBlue;">MediumSlateBlue</option>-->
<!--                                        <option value="MediumSpringGreen" style="color:MediumSpringGreen;">MediumSpringGreen</option>-->
<!--                                        <option value="MediumTurquoise" style="color:MediumTurquoise;">MediumTurquoise</option>-->
<!--                                        <option value="MediumVioletRed" style="color:MediumVioletRed;">MediumVioletRed</option>-->
<!--                                        <option value="MidnightBlue" style="color:MidnightBlue;">MidnightBlue</option>-->
<!--                                        <option value="MintCream" style="color:MintCream;">MintCream</option>-->
<!--                                        <option value="MistyRose" style="color:MistyRose;">MistyRose</option>-->
<!--                                        <option value="Moccasin" style="color:Moccasin;">Moccasin</option>-->
<!--                                        <option value="NavajoWhite" style="color:NavajoWhite;">NavajoWhite</option>-->
<!--                                        <option value="Navy" style="color:Navy;">Navy</option>-->
<!--                                        <option value="OldLace" style="color:OldLace;">OldLace</option>-->
<!--                                        <option value="Olive" style="color:Olive;">Olive</option>-->
<!--                                        <option value="OliveDrab" style="color:OliveDrab;">OliveDrab</option>-->
<!--                                        <option value="Orange" style="color:Orange;">Orange</option>-->
<!--                                        <option value="OrangeRed" style="color:OrangeRed;">OrangeRed</option>-->
<!--                                        <option value="Orchid" style="color:Orchid;">Orchid</option>-->
<!--                                        <option value="PaleGoldenRod" style="color:PaleGoldenRod;">PaleGoldenRod</option>-->
<!--                                        <option value="PaleGreen" style="color:PaleGreen;">PaleGreen</option>-->
<!--                                        <option value="PaleTurquoise" style="color:PaleTurquoise;">PaleTurquoise</option>-->
<!--                                        <option value="PaleVioletRed" style="color:PaleVioletRed;">PaleVioletRed</option>-->
<!--                                        <option value="PapayaWhip" style="color:PapayaWhip;">PapayaWhip</option>-->
<!--                                        <option value="PeachPuff" style="color:PeachPuff;">PeachPuff</option>-->
<!--                                        <option value="Peru" style="color:Peru;">Peru</option>-->
<!--                                        <option value="Pink" style="color:Pink;">Pink</option>-->
<!--                                        <option value="Plum" style="color:Plum;">Plum</option>-->
<!--                                        <option value="PowderBlue" style="color:PowderBlue;">PowderBlue</option>-->
<!--                                        <option value="Purple" style="color:Purple;">Purple</option>-->
<!--                                        <option value="Red" style="color:Red;">Red</option>-->
<!--                                        <option value="RosyBrown" style="color:RosyBrown;">RosyBrown</option>-->
<!--                                        <option value="RoyalBlue" style="color:RoyalBlue;">RoyalBlue</option>-->
<!--                                        <option value="SaddleBrown" style="color:SaddleBrown;">SaddleBrown</option>-->
<!--                                        <option value="Salmon" style="color:Salmon;">Salmon</option>-->
<!--                                        <option value="SandyBrown" style="color:SandyBrown;">SandyBrown</option>-->
<!--                                        <option value="SeaGreen" style="color:SeaGreen;">SeaGreen</option>-->
<!--                                        <option value="SeaShell" style="color:SeaShell;">SeaShell</option>-->
<!--                                        <option value="Sienna" style="color:Sienna;">Sienna</option>-->
<!--                                        <option value="Silver" style="color:Silver;">Silver</option>-->
<!--                                        <option value="SkyBlue" style="color:SkyBlue;">SkyBlue</option>-->
<!--                                        <option value="SlateBlue" style="color:SlateBlue;">SlateBlue</option>-->
<!--                                        <option value="SlateGray" style="color:SlateGray;">SlateGray</option>-->
<!--                                        <option value="Snow" style="color:Snow;">Snow</option>-->
<!--                                        <option value="SpringGreen" style="color:SpringGreen;">SpringGreen</option>-->
<!--                                        <option value="SteelBlue" style="color:SteelBlue;">SteelBlue</option>-->
<!--                                        <option value="Tan" style="color:Tan;">Tan</option>-->
<!--                                        <option value="Teal" style="color:Teal;">Teal</option>-->
<!--                                        <option value="Thistle" style="color:Thistle;">Thistle</option>-->
<!--                                        <option value="Tomato" style="color:Tomato;">Tomato</option>-->
<!--                                        <option value="Turquoise" style="color:Turquoise;">Turquoise</option>-->
<!--                                        <option value="Violet" style="color:Violet;">Violet</option>-->
<!--                                        <option value="Wheat" style="color:Wheat;">Wheat</option>-->
<!--                                        <option value="White">White</option>-->
<!--                                        <option value="WhiteSmoke" style="color:WhiteSmoke;">WhiteSmoke</option>-->
<!--                                        <option value="Yellow" style="color:Yellow;">Yellow</option>-->
<!--                                        <option value="YellowGreen" style="color:YellowGreen;">YellowGreen</option>-->
<!--                                    </select>-->
<!--                                </div>-->

                                <div class="my-3">
                                    <label for="group_dates">Ημέρες και ώρες</label>
                                    <div v-for="(week_day, index) in form.week_days" id="group_dates" class="form-group wave-group-dates mx-0 mb-3 row">
                                        <div class="mt-2 col-sm-12 col-lg-4">
                                            <label :for="'day'+index">Ημέρα</label>
                                            <select v-model="week_day.day" :id="'day'+index" :name="'day'+index" class="form-select mb-3" required>
                                                <option value="" hidden>Επιλογή ημέρας</option>
                                                <option value=MONDAY>Δευτέρα</option>
                                                <option value=TUESDAY>Τρίτη</option>
                                                <option value=WEDNESDAY>Τετάρτη</option>
                                                <option value=THURSDAY>Πέμπτη</option>
                                                <option value=FRIDAY>Παρασκευή</option>
                                                <option value=SATURDAY>Σάββατο</option>
                                                <option value=SUNDAY>Κυριακή</option>
                                            </select>
                                        </div>

                                        <div class="my-2 col-sm-12 col-lg-4">
                                            <label :for="'start_time'+index">Ώρα έναρξης</label>
                                            <input v-model="week_day.start_time" :id="'start_time'+index" :name="'start_time'+index" type="time" class="form-control" placeholder="Ώρα έναρξης" required>
                                        </div>

                                        <div class="my-2 col-sm-12 col-lg-4">
                                            <label :for="'end_time'+index">Ώρα λήξης</label>
                                            <input v-model="week_day.end_time" :id="'end_time'+index" :name="'end_time'+index" type="time" class="form-control" placeholder="Ώρα λήξης" required>
                                        </div>

                                        <div class="mb-2">
                                            <b-button @click="removeDate(index)" class="btn btn-primary danger-button-color-wave float-right" :disabled="form.week_days.length === 1">
                                                Αφαίρεση
                                            </b-button>
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <b-button @click="addDate" class="btn btn-primary button-color-wave float-right">Πρόσθεσε ημερομηνία</b-button>
                                </div>

                                <div class="mt-6">
                                    <b-button class="btn btn-primary button-color-wave" type="submit" variant="primary">Αποθήκευση</b-button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                form: {
                    name: null,
                    description: null,
                    teacher: null,
                    number_of_students: 0,
                    color: "White",
                    week_days: [
                        {
                            day: "",
                            start_time: null,
                            end_time: null
                        }
                    ]
                }
            }
        },
        methods:{
            submitForm() {
                this.form.number_of_students = parseInt(this.form.number_of_students);

                axios.post('/admin/gym-classes', this.form)
                    .then((result) => {
                        //Perform Success Action
                        this.$router.push({ name: 'ShowGymClasses' });

                        // display success message
                        this.$alertHandler.showAlert('Class created successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            addDate() {
                this.form.week_days.push({day:"", start_time:null, end_time:null}); // what to push unto the rows array?
            },
            removeDate(index) {
                if (this.form.week_days.length > 1) {
                    this.form.week_days.splice(index, 1)
                }
            }
        }
    }
</script>
