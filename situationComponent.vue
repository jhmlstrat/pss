var SituationComponent = {
  name: 'SituationComponent',
  template: `
    <div class='container'>
      <b-row no-gutters style="background-color: white;">
        <b-col cols="2" v-bind:style="getStyle('visitor')" class="text-center">
          <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.visitor.name}} {{ gameInfo.situation.situation.runsV }}</span>
        </b-col>
        <b-col cols="2" v-bind:style="getStyle('home')" class="text-center">
          <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.home.name}} {{ gameInfo.situation.situation.runsH }}</span>
        </b-col>
        <b-col cols="2" class="text-center">
          <b-row no-gutters>
            <b-col cols="11" class="border border-dark border-top-0">
            {{ gameInfo.situation.situation.side == 0 ? 'Top' : 'Bot' }} {{ gameInfo.situation.situation.inning }}
            </b-col>
          </b-row>
          <b-row no-gutters>
            <b-col cols="11" class="border border-dark border-bottom-0">
            Outs: {{ gameInfo.situation.situation.outs }}
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2 pt-1 pb-1" >
          <b-row no-gutters class="mt-1">
            <b-col cols="4">
            </b-col>
            <b-col cols="4">
              <b-icon icon="square" rotate="45" font-scale="0.8" v-if="gameInfo.situation.situation.second == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="0.8" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.second" v-else></b-icon>
            </b-col>
            <b-col cols="4">
            </b-col>
          </b-row>
          <b-row no-gutters>
            <b-col cols="1">
            </b-col>
            <b-col cols="3" class="text-right">
              <b-icon icon="square" rotate="45" font-scale="0.8" v-if="gameInfo.situation.situation.third == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="0.8" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.third" v-else></b-icon>
            </b-col>
            <b-col cols="2">
            </b-col>
            <b-col cols="4" class="text-left">
              <b-icon icon="square" rotate="45" font-scale="0.8" v-if="gameInfo.situation.situation.first == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="0.8" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.first" v-else></b-icon>
            </b-col>
            <b-col cols="2">
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="4">
          <b-row style="background-color: white;">
            <b-col cols="12" class="border border-dark border-top-0 border-right-0">
Pitcher: {{ gameInfo.situation.situation.pitcher }}
            </b-col>
          </b-row>
          <b-row style="background-color: white;">
            <b-col cols="12" class="border border-dark border-bottom-0 border-right-0">
Batter: {{ gameInfo.situation.situation.batter }}
            </b-col>
          </b-row>
        </b-col>
      </b-row>
    </div>
  `,
  props: ['gameInfo'],
  data: function() {
    return {
      team: {'team_name':''},
      teamname: 't',
    }
  },
  watch: {
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
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
  },
};

