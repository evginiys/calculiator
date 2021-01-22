<template>
    <div class="result">
        <div class="for-values">
        <div class="value">
            <h3>Стоимость недвижимости</h3>
            <p>{{priceObject}} руб</p>
        </div>
        <div class="value">
            <h3>переплата</h3>
            <p>{{overPayment}} руб</p>
        </div>
        <hr>
        <div class="value">
            <h3>Ежемесячный платеж</h3>
            <p>{{paymentPerMonth}} руб</p>
        </div>
        <div class="value">
            <h3>Общая сумма выплат</h3>
            <p>{{priceTotal}} руб</p>
        </div>
        <hr>
        <div class="value">
            <h3>Процентная ставка</h3>
            <p>{{percent}}</p>
        </div>
            <div class="value">
                <h3>Длительность</h3>
                <p>{{term}} мес</p>
            </div>
    </div>
        <div class="block">
            <a class="button7"  :href="this.linkExel">Отчет xlsx</a>
            <a class="button7"  :href="this.linkPdf">Отчет pdf</a>
        </div>
    </div>
</template>

<script>
import Bus from "../Bus";
    export default {
        name: 'Result',
        data: function(){
            return {
                firstPayment:0,
                paymentPerMonth:0,
                priceObject:0,
                priceTotal:0,
                percent:0,
                term:0,
                overPayment:0,
                params:'',
                link:process.env.VUE_APP_SERVER_ROOT+"report.php",
                linkExel:process.env.VUE_APP_SERVER_ROOT+"report.php",
                linkPdf:process.env.VUE_APP_SERVER_ROOT+"report.php",
                errors:{
                        error:false,
                        message:''
                },
            }
        },
        props: {

            info:{
                type:String,
                default:"empty"
            }
        },
        created() {
            Bus.$on('getResult',(data)=> {
                this.params=data;
                this.linkPdf=this.link+'?'+(new URLSearchParams(this.params));
                this.params['xlsx']=1;
                this.linkExel=this.link+'?'+(new URLSearchParams(this.params));
                console.log(new URLSearchParams(this.params));
                this.result();
            });
        },
        methods: {
            result: function () {
                console.log('start query');
                const axios = require('axios');
                let params=new URLSearchParams(this.params);
                axios.get(process.env.VUE_APP_SERVER_ROOT+"calculateMortgage.php",{params})
                    .then(response => {
                        console.log(response.data)
                        if (!response.data.error) {
                            this.firstPayment = response.data.data.firstPayment;
                            this.paymentPerMonth = response.data.data.everyMonthPayment;
                            this.priceObject = response.data.data.price;
                            this.priceTotal = response.data.data.totalPay;
                            this.percent = response.data.data.percent;
                            this.overPayment=response.data.data.overPayment;
                            this.term=response.data.data.term;
                        } else {
                            this.errors.error = true;
                            this.errors.message = response.data.errors.message;
                        }
                    });
                console.log(this.info);
                console.log('end query');
            },
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    .value{
        min-width: 100px;
        max-width: 200px;
        width: 45%;
        padding: 2%;
    }
    .value h3{
        color: white;
        font-size: 15px;
    }
    .value p{
        color: white;
        font-size: 20px;
    }
    hr{
        color: black;
        width: 100%;
    }
    .result{
        background: linear-gradient(60deg, #8df1d5, #00ccff);
        border-radius: 20px;
        color: #b9c1ca;
    }
    .for-values{
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    a.button7 {
        font-weight: 700;
        color: white;
        text-decoration: none;
        padding: .8em 1em calc(.8em + 3px);
        border-radius: 5px;
        background: rgb(64,199,129);
        box-shadow: 0 -3px rgb(53,167,110) inset;
        transition: 0.2s;
    }
    a.button7:hover { background: rgb(53, 167, 110); }
    a.button7:active {
        background: rgb(33,147,90);
        box-shadow: 0 3px rgb(33,147,90) inset;
    }
</style>
