<template>
    <div class="fields">
        <div class="block">
            <select v-model="selected" @change="getIntertypes(selected.id)">
                <option v-for="variant in variants" :key="variant.id" :value="variant">
                    {{ variant.name }}
                </option>
            </select>
        </div>
        <div class="block">
            <h3>Стлимость объекта:</h3>
            <input v-model="price"/>
            <input type="range" :min="priceMin" :max="priceMax" step="1000" v-model="price">
            <div>
                <span>{{priceMin}}руб.</span><span>{{priceMax}}руб.</span>
            </div>
        </div>
        <div class="block">
            <h3>Первый взнос:</h3>
            <div class="procents">
                <input :value="firstPaymentInRub"/><span>{{first}}%</span>
            </div>
            <input type="range" :min="firstMin" :max="firstMax" step="1" v-model="first">
            <div>
                <span>{{firstMin}}%</span><span>{{firstMax}}%</span>
            </div>
        </div>
        <div class="block">
            <h3>Срок ипотеки:</h3>
            <input v-model="term"/>
            <input type="range" :min="termMin" :max="termMax" step="1" v-model="term">
            <div>
                <span>{{termMin}}мес.</span><span>{{termMax}}мес.</span>
            </div>
        </div>
        <div class="block">
            <div class="checkBox" v-for="intertype in intertypes" :key="intertype.intertype">
                <input type="radio" name="test" :checked="intertype.intertype==0" @change="getOption(selected.id,selectedIntertype)" :id="intertype.intertype"
                       :value="intertype.intertype" v-model="selectedIntertype">
                <label :for="intertype.intertype" v-if="intertype.intertype==0">
                    обычная
                </label>
                <label :for="intertype.intertype" v-if="intertype.intertype==1">
                    семейная
                </label>
                <label :for="intertype.intertype" v-if="intertype.intertype==2">
                    военная
                </label>
            </div>
            <br>

        </div>
    </div>
</template>

<script>
    import Bus from "../Bus";

    export default {
        name: 'Fields',
        data: function () {
            return {
                variants: [
                    {id: 1, name: "test1"},
                ],
                selected: {id: 0, name: "выбирете тип"},
                first: 1,
                firstMin: 10,
                firstMax: 25,
                price: 90_000_000,
                priceMin: 9_000_000,
                priceMax: 900_000_000,
                termMin: 36,
                term: 36,
                termMax: 300,

                paymentPerMonth: 0,
                priceObject: 1000000,
                priceTotal: 0,
                percent: 0,
                selectedIntertype: 1,
                intertypes: {
                    intertype: 0,
                },
                errors: {
                    error: false,
                    message: ''
                },
            }

        },
        props: {},
        created() {
            console.log('start query created');
            const axios = require('axios');
            axios.get(process.env.VUE_APP_SERVER_ROOT + 'types.php')
                .then(response => {
                    if (!response.data.errors.error) {
                        this.variants = response.data.data;
                        console.log(response.data.data);
                    } else {
                        this.errors = response.data.errors;
                    }
                });
            console.log('end query');
        },
        updated() {
            // let params = new URLSearchParams();
            // params.append('typeId', this.selected.id);
            // params.append('interType', this.selectedIntertype);
            // params.append('term', this.term);
            // params.append('price', this.price);
            // params.append('firstPayment', this.firstPayment);
            // params.append('percent', this.percent);
            let params = {
                'typeId': this.selected.id,
                'interType': this.selectedIntertype,
                'term': this.term,
                'price': this.price,
                'firstPayment': this.first,
                'percent': this.percent
            };
            Bus.$emit('getResult', params);
        },
        methods: {
            getIntertypes: function (typeId
            ) {
                let params = new URLSearchParams();
                params.append('typeId', typeId);
                this.getOption(typeId);
                console.log('params to getResult Request'.params);
                const axios = require('axios');
                axios.get(process.env.VUE_APP_SERVER_ROOT + 'intertypes.php', {params})
                    .then(response => {
                        if (!response.data.errors.error) {
                            this.intertypes = response.data.data;
                        } else {
                            this.errors.error = true;
                            this.errors.message = response.data.message;
                        }
                    });
                console.log(this.info);
                console.log('end query');
            },
            getOption: function (typeId, intertype = 0) {
                let params = new URLSearchParams();

                params.append('intertype', intertype);
                params.append('typeId', typeId);

                console.log('params to getResult Request'.params);
                const axios = require('axios');
                axios.get(process.env.VUE_APP_SERVER_ROOT + 'options.php', {params})
                    .then(response => {
                        if (!response.data.errors.error) {
                            console.log(response.data.data);
                            this.firstMin = response.data.data[0].firstpaymentmin;
                            this.firstMax = response.data.data[0].firstpaymentmax;
                            this.first = this.firstMin;
                            this.priceMin = response.data.data[0].pricemin;
                            this.priceMax = response.data.data[0].pricemax;
                            this.price = this.priceMin;
                            this.termMin = response.data.data[0].termmin;
                            this.termMax = response.data.data[0].termmax;
                            this.term = response.data.data[0].termmin;
                            this.percent = response.data.data[0].percent;
                        } else {
                            this.errors.error = true;
                            this.errors.message = response.data.message;
                        }
                    });
                console.log(this.info);
                console.log('end query');
            }
        },
        computed: {
            firstPaymentInRub: function () {
                return this.price * this.first / 100;
            }
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    .checkBox {
        width: 20%;
        display: inline;
        float: left;
    }

    .block {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
    }

    select {
        width: 200px;
        height: 36px;
        border-radius: 10px;
        -webkit-appearance: none;
        background-image: url('../assets/arrow.png');
        background-position: right center;
        background-repeat: no-repeat;
        line-height: 1em;
    }

    .block div {
        width: 90%;
        display: flex;
        flex-direction: row;
        align-content: center;
        justify-content: space-between;
    }

    .block div span {
        padding-top: 2%;
    }

    .procents span {
        transform: translateX(-208%);
        color: green;
    }

    .block .procents {
        width: 100%;
    }

    .procents input {
        width: 100%;
    }

    input {
        width: 90%;
        font-size: 20px;
        padding: 8px 0 8px 10px;
        border: 1px solid #cecece;
        background: #F6F6f6;
        border-radius: 8px;
        margin-right: 3%;
    }

    .fields {
        background: #b9c1ca;
        border-radius: 20px;
        padding: 20px;
    }
</style>
