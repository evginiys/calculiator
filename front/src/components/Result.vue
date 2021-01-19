<template>
    <div class="result">
        <div class="for-values">
        <div class="value">
            <h3>Стоимость недвижимости</h3>
            <p>{{priceObject}} руб</p>
        </div>
        <div class="value">
            <h3>Первоначальный взнос</h3>
            <p>{{firstPayment}} руб</p>
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
            <p>{{percent}} руб</p>
        </div>
    </div>
        <div class="block">
            <a class="button7" @click="testApi" href="#">Получить отчет</a>
        </div>
    </div>

</template>

<script>

    export default {
        name: 'Result',
        props: {
            firstPayment:{
                type:Number,
                default:0
            },
            paymentPerMonth:{
                type:Number,
                default:0
            },
            priceObject:{
                type:Number,
                default:0
            },
            priceTotal:{
                type:Number,
                default:0
            },
            percent:{
                type:Number,
                default:0
            },
            info:{
                type:String,
                default:"empty"
            },
            errors:{
                type:Object,
                default: {
                    error:false,
                    message:''
                }
            },
        },
        methods:{
            testApi:function () {
                console.log('start query');
                const axios=require('axios');
                axios.get('http://back.com:9500/api/public/test.php')
                    .then(response => {
                        this.info=response.data;
                        if (!response.data.error) {
                            this.firstPayment = response.data.firstPayment;
                            this.paymentPerMonth = response.data.paymentPerMonth;
                            this.priceObject = response.data.priceObject;
                            this.priceTotal = response.data.priceTotal;
                            this.percent = response.data.percent;
                        }else {
                            this.errors.error=true;
                            this.errors.message=response.data.message;
                        }
                    });
                console.log(this.info);
                console.log('end query');
            }
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
