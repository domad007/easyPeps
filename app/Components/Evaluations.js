import React, { Component, useRef } from 'react';
import 
{ 
    StyleSheet, 
    View, 
    Text, 
    ScrollView, 
    AsyncStorage, 
    Alert, 
    ActivityIndicator, 
    Animated 
} from 'react-native';

class Evaluations extends Component {
    constructor(props){
        super(props);
        this.state = {
            evaluations: []
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idGroup').then(this.evaluations);
    }
    evaluations = (value) => {
        fetch('http://192.168.1.3/evaluationUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                Alert.alert(
                    "Problème d'évaluations",
                    "Vous n'avez pas d'évaluation, veuillez en créer sur notre site web",
                    [
                        {
                            text: "OK", onPress: () => this.props.navigation.navigate("MenuGroup") 
                        }
                    ]
                );

            }
            else {
                this.setState({evaluations: responseJson})
            }
        })
    }
    render(){
        let evaluations = this.state.evaluations
        let afficheEval = [];
        if(evaluations.length === 0) {
            return( 
                <View style={style.loading}>
                    <ActivityIndicator size="large" color="red" />
                </View>
            )
        } 
        const FadeInView = (props) => {
            const fadeAnim = useRef(new Animated.Value(0)).current  // Initial value for opacity: 0
          
            React.useEffect(() => {
              Animated.timing(
                fadeAnim,
                {
                  toValue: 1,
                  duration: 800,
                }
              ).start();
            }, [])
          
            return (
              <Animated.View                 // Special animatable View
                style={{
                  ...props.style,
                  opacity: fadeAnim,         // Bind opacity to animated value
                }}
              >
                {props.children}
              </Animated.View>
            );
          }
        for(let i = 0; i<evaluations.length; i++){
            afficheEval.push(
                <FadeInView>
                    <View key = { i } style={style.container}>
                        <Text style={{ fontSize: 20, color: 'white' }}>{ evaluations[i]['evaluation'] }</Text>
                        <Text style={{color: 'white'}}>Date de l'évaluation: { evaluations[i]['date_evaluation'] }</Text>
                        <Text style={{color: 'white'}}>Herues d'évaluation: { evaluations[i]['heures'] }</Text>
                        <Text style={{color: 'white'}}>Compétence: { evaluations[i]['competence'] }</Text>
                        <Text style={{color: 'white'}}>Cotation: { evaluations[i]['sur_combien'] }</Text>
                        <Text style={{color: 'white'}}>Periode: { evaluations[i]['periode'] }</Text>
                    </View>
                </FadeInView>
            )
        }
        return (
            <ScrollView>
                { afficheEval }
            </ScrollView>
        )
    }
}

const style= StyleSheet.create({
    container: {
        flex : 1,
        backgroundColor: 'red',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 10,
        marginLeft: 10,
        width: '95%',
        borderRadius: 25,
        marginVertical: 10,
        paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    },
    loading: {
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
    }
})
export default Evaluations