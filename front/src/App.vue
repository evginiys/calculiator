<template>
  <div id="app">

    <Fields msg="My Component" class="component"/>
    <Result class="component"></Result>

  </div>
</template>

<script>
//import HelloWorld from './components/HelloWorld.vue'
import Fields from './components/Fields.vue'
import Result from './components/Result.vue'
export default {
  name: 'App',
  components: {
    Fields,
    Result
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-content: center;
  flex-wrap: wrap;
}
.component{
  width: 45%;
  min-width: 300px;
}

</style>
