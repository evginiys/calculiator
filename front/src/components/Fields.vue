<template>
    <div class="fields">
        <div class="block">
            <option>Выбирете нужный тип ипотеки</option>
            <select v-model="selected">
                <option v-for="variant in variants" :key="variant.id" >
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
                <input :value="firstPayment"/><span>{{first}}%</span>
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
    </div>
</template>

<script>
    export default {
        name: 'Fields',
        props: {
            variants:{
                type:Array,
                default:function() {
                    return [
                        {id: 1, name: "test1"},
                        {id: 2, name: "test2"},
                        {id: 3, name: "test3"},
                        {id: 4, name: "test4"},
                    ];
                }

            },
            selected:{
              type:String,
              default:"выбирете вариант ипотеки"
            },
            first: {
                type: Number,
                default: 75
            },
            firstMin: {
                type: Number,
                default: 75
            },
            firstMax: {
                type: Number,
                default: 100
            },
            price: {
                type: Number,
                default: 10000000
            },
            priceMin: {
                type: Number,
                default: 100000
            },
            priceMax: {
                type: Number,
                default: 100000000
            },
            term: {
                type: Number,
                default: 100
            },
            termMin: {
                type: Number,
                default: 36
            },
            termMax: {
                type: Number,
                default: 300
            },
        },
        computed: {
            firstPayment: function () {
                return this.price * this.first / 100;
            }
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
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
    .fields{
        background: #b9c1ca;
        border-radius: 20px;
        min-width: 200px;
        padding: 20px;
    }
</style>
