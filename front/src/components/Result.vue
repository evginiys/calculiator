<template>
    <div class="result">
        <div class="value">
            <h3>Стоимость недвижимости</h3>
            <p>1000000 руб</p>
        </div>
        <div class="value">
            <h3>Первоначальный взнос</h3>
            <p>1000000 руб</p>
        </div>
        <hr>
        <div class="value">
            <h3>Ежемесячный платеж</h3>
            <p>1000000 руб</p>
        </div>
        <div class="value">
            <h3>Общая сумма выплат</h3>
            <p>1000000 руб</p>
        </div>
        <hr>
        <div class="value">
            <h3>Процентная ставка</h3>
            <p>1000000 руб</p>
        </div>
        <div class="value">
        </div>
        <div class="block">
            <a class="button7" href="#">Получить отчет</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Result',
        props: {

        },

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
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: flex-start;
        flex-wrap: wrap;
        background: linear-gradient(60deg, #8df1d5, #00ccff);
        border-radius: 20px;
        min-width: 200px;
        color: #b9c1ca;
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
