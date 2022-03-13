var GameComponent = {
  name: 'GameComponent',
  template: `
    <div class='container'>
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <hr/>
      <b-row v-show="gameInfo.situation && gameInfo.situation.situation.gameOver">
        <b-col cols='12'>
          <p class='h1 text-center'>Game Over</p>
        </b-col>
      </b-row>
      <b-row no-gutters>
        <b-col cols='6'>
          <situation-component v-bind:gameInfo="gameInfo"
                             v-bind:vRoster="team.team == gameInfo.visitor.name ? roster : oRoster"
                             v-bind:hRoster="team.team == gameInfo.visitor.name ? oRoster : roster">
          </situation-component>
        </b-col>
        <b-col cols='6'>
          <line-score-component v-bind:gameInfo="gameInfo"></line-score-component>
        </b-col>
      </b-row>
      <hr/>
      <b-row v-show="gameInfo.situation && ! gameInfo.situation.situation.gameOver && ! relevantInjury()">
        <b-col cols='12'>
          <infoline-component v-bind:gameInfo="gameInfo"
                             v-bind:vRoster="team.team == gameInfo.visitor.name ? roster : oRoster"
                             v-bind:hRoster="team.team == gameInfo.visitor.name ? oRoster : roster">
          </infoline-component>
        </b-col>
      </b-row>
      <b-row v-show="gameInfo.situation && ! gameInfo.situation.situation.gameOver && ! relevantInjury()">
        <b-col cols='12'>
          <game-entry-component v-bind:gameInfo="gameInfo"></game-entry-component>
        </b-col>
      </b-row>
      <b-row class='mt-5' v-show="gameInfo.situation && ! gameInfo.situation.situation.gameOver && ! relevantInjury()">
        <b-col cols='12'>
          <defense-component v-bind:gameInfo="gameInfo" 
                             v-bind:vRoster="team.team == gameInfo.visitor.name ? roster : oRoster"
                             v-bind:hRoster="team.team == gameInfo.visitor.name ? oRoster : roster">
          </defense-component>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
    }
  },
  components: {
    'game-entry-component': GameEntryComponent,
    'line-score-component': LineScoreComponent,
    'situation-component': SituationComponent,
    'defense-component': DefenseComponent,
    'infoline-component': InfoLineComponent,
  },
  watch: {
  },
  mounted() {
  },
  computed: {
    gameInfo() { if (vue == undefined) return {'visitor':{'name':'','gameNumber':''},'home':{'name':'','gameNumber':''}}; return vue.gameInfo; },
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    team() { if (vue == undefined) return {'team':'None'}; return vue.team; },
    roster() { if (vue == undefined) return {}; return vue.roster; },
    oRoster() { if (vue == undefined) return {}; return vue.oRoster; },
  },
  methods: {
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
      vue.loadSchedule();
    },
    relevantInjury() {
      if (this.gameInfo.situation.situation.side == 0) {
        //Runner on 1st injured (replace via situation)
        if (this.gameInfo.situation.situation.first != '') {
          for (let i=0; i < this.gameInfo.visitor.lineup.length; i++) {
            ll = this.gameInfo.visitor.lineup[i].length - 1;
            if (this.gameInfo.visitor.lineup[i][ll].player.name == this.gameInfo.situation.situation.first) {
              if ("injured" in this.gameInfo.visitor.lineup[i][ll].player) {
                if (this.gameInfo.visitor.lineup[i][ll].player.injured) return true;
              }
              i = this.gameInfo.visitor.lineup.length;
            }
          }
        }
        //Batter injured DH revisited (replace via situation)
        for (let i=0; i < this.gameInfo.visitor.lineup.length; i++) {
          ll = this.gameInfo.visitor.lineup[i].length - 1;
          if (ll < 0) continue;
          if (this.gameInfo.visitor.lineup[i][ll].player.name == this.gameInfo.situation.situation.batter) {
            if ("injured" in this.gameInfo.visitor.lineup[i][ll].player) {
              if (this.gameInfo.visitor.lineup[i][ll].player.injured) return true;
            }
            i = this.gameInfo.visitor.lineup.length;
          }
        }
        //Pitcher injured (replace via situation)
        pl = this.gameInfo.home.rotation.length - 1;
        if ("injured" in this.gameInfo.home.rotation[pl].player) {
          if (this.gameInfo.home.rotation[pl].player.injured) return true;
        }
        //Opposition defense injured (go to lineup)
        for (let i=0; i < this.gameInfo.home.lineup.length; i++) {
          ll = this.gameInfo.home.lineup[i].length - 1;
          if (ll < 0) continue;
          //console.log(ll);
          pl = this.gameInfo.home.lineup[i][ll].player.positions.length - 1;
          //console.log(pl);
          if (this.gameInfo.home.lineup[i][ll].player.positions[pl].position.pos != 'DH') {
            if ("injured" in this.gameInfo.home.lineup[i][ll].player) {
              if (this.gameInfo.home.lineup[i][ll].player.injured) {
                vue.currentComponent='lineupsComponent';
                return true;
              }
            }
          }
        }
      } else {
        //Runner on 1st injured (replace via situation)
        if (this.gameInfo.situation.situation.first != '') {
          for (let i=0; i < this.gameInfo.home.lineup.length; i++) {
            ll = this.gameInfo.home.lineup[i].length - 1;
            if (ll < 0) continue;
            if (this.gameInfo.home.lineup[i][ll].player.name == this.gameInfo.situation.situation.first) {
              if ("injured" in this.gameInfo.home.lineup[i][ll].player) {
                if (this.gameInfo.home.lineup[i][ll].player.injured) return true;
              }
              i = this.gameInfo.home.lineup.length;
            }
          }
        }
        //Batter injured DH revisited (replace via situation)
        for (let i=0; i < this.gameInfo.home.lineup.length; i++) {
          ll = this.gameInfo.home.lineup[i].length - 1;
          if (ll < 0) continue;
          if (this.gameInfo.home.lineup[i][ll].player.name == this.gameInfo.situation.situation.batter) {
            if ("injured" in this.gameInfo.home.lineup[i][ll].player) {
              if (this.gameInfo.home.lineup[i][ll].player.injured) return true;
            }
            i = this.gameInfo.home.lineup.length;
          }
        }
        //Pitcher injured (replace via situation)
        pl = this.gameInfo.visitor.rotation.length - 1;
        if ("injured" in this.gameInfo.visitor.rotation[pl].player) {
          if (this.gameInfo.visitor.rotation[pl].player.injured) return true;
        }
        //Opposition defense injured (go to lineup)
        for (let i=0; i < this.gameInfo.visitor.lineup.length; i++) {
          ll = this.gameInfo.visitor.lineup[i].length - 1;
          if (ll < 0) continue;
          //console.log(ll);
          pl = this.gameInfo.visitor.lineup[i][ll].player.positions.length - 1;
          //console.log(pl);
          if (this.gameInfo.visitor.lineup[i][ll].player.positions[pl].position.pos != 'DH') {
            if ("injured" in this.gameInfo.visitor.lineup[i][ll].player) {
              if (this.gameInfo.visitor.lineup[i][ll].player.injured) {
                vue.currentComponent='lineupsComponent';
                return true;
              }
            }
          }
        }
      }
      return false;
    },
  },
};

