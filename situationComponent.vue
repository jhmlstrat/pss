var SituationComponent = {
  name: 'SituationComponent',
  template: `
    <div class='container'>
      <b-row no-gutters style="background-color: white;">
        <b-col cols="2" v-bind:style="getStyle('visitor')" class="text-center">
          <b-row no-gutters>
            <b-col cols="12" class="pt-2">
              <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.visitor.name}} {{ gameInfo.situation.situation.runsV }}</span>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2" v-bind:style="getStyle('home')" class="text-center">
          <b-row no-gutters>
            <b-col cols="12" class="pt-2">
              <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.home.name}} {{ gameInfo.situation.situation.runsH }}</span>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2" class="text-center border border-dark border-top-0 border-bottom-0">
          <b-row no-gutters>
            <b-col cols="12" class="border-bottom border-dark mt-2 pb-1">
            {{ gameInfo.situation.situation.side == 0 ? 'Top' : 'Bot' }} {{ gameInfo.situation.situation.inning }}
            </b-col>
          </b-row>
          <b-row no-gutters>
            <b-col cols="12" class="border-top border-dark pt-2">
            Outs: {{ gameInfo.situation.situation.outs }}
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2" class="pt-1 pb-1 border border-dark border-top-0 border-bottom-0" >
          <b-row no-gutters class="mt-1">
            <b-col cols="3">
            </b-col>
            <b-col cols="4" class="text-center">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.second == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.second" v-on:click="doPinchRunner(2)" v-else></b-icon>
            </b-col>
            <b-col cols="3">
            </b-col>
          </b-row>
          <b-row no-gutters class="mt-2">
            <b-col cols="3" class="text-left">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.third == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.third" v-on:click="doPinchRunner(3)" v-else></b-icon>
            </b-col>
            <b-col cols="2">
            </b-col>
            <b-col cols="3" class="text-right">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.first == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.first" v-on:click="doPinchRunner(1)" v-else></b-icon>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="4" class="border border-dark border-top-0 border-bottom-0">
              <b-button class="pr-0 pl-0 mr-0 ml-0 text-left" variant="link" href="#" v-on:click="doReliever()">
Pitch: {{ gameInfo.situation.situation.pitcher.substring(0,12) }}
              </b-button>
          <b-row no-gutters style="background-color: white;">
            <b-col cols="12" class="border-top border-dark text-left">
              <b-button class="pr-0 pl-0 pt-0 pb-0" variant="link" href="#" v-on:click="doPinchHitter()">
Bat: {{ gameInfo.situation.situation.batter.substring(0,12) }}
              </b-button>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
    </div>
  `,
  props: ['gameInfo'],
  data: function() {
    return {
    }
  },
  watch: {
  },
  mounted() {
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    getStyle(s) {
      if (s=='visitor') t=this.gameInfo.visitor.name;
      else t=this.gameInfo.home.name;
      style = 'background-color: white;';
      for (p of vue.config.colors) {
        if (Object.keys(p)[0] == t) style = 'background-color: ' + Object.values(p)[0] + '; padding-top: 0.8rem !important;';
      }
      return style;
    },
    doPinchRunner(base) {
      console.log('doPinchRunner - ' + base);
//TBD
    },
    doReliever() {
      console.log('doReliever');
//TBD
    },
    doPinchHitter() {
      console.log('doPinchHitter');
//TBD
    },
  },
};

