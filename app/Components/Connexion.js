import React, { Component } from 'react';
import 
{ 
    StyleSheet, 
    View, 
    TextInput, 
    Image, 
    TouchableOpacity, 
    Text, 
    ScrollView, 
    AsyncStorage,
    Alert,
    ActivityIndicator 
} from 'react-native';
import axios from 'axios'

export default class Connexion extends Component {
    constructor(props) {
        super(props)
        this.state = {
            pseudo: '',
            mdp: '',
        }
    }
    componentWillUnMount(){
        this.props.navigation.navigate('ChoixGroup')
    }
    connexion = async ()=> {
        const { pseudo } = this.state;
        const { mdp } = this.state;
        let user = await axios.post('https://easypeps.be/connexionAppli', 
            {
                pseudo: pseudo,
                mdp: mdp
            }
        )
        if(user.data == "problemUser"){
            Alert.alert(
                "",
                "Votre pseudo semble incorrect, veuillez réessayer"
            );
        }
        else if(user.data  == "problemPassword"){
            Alert.alert(
                "",
                "Votre mot de passe semble incorrect, veuillez réessayer"
            )
        }
        else {
            AsyncStorage.clear()
            AsyncStorage.setItem('idUser', JSON.stringify(user.data['id']))
            this.componentWillUnMount()
        }
    }
    render(){
        return (
            <ScrollView>
                <View style={style.container}>  
                    <Image 
                        style={{ width: 150, height: 150, marginTop: -50 }} 
                        source={ require('../images/favicon.png') }
                    />    
                    <Text style={{ fontSize: 30 }}>easyPeps</Text> 
                    <TextInput 
                        style={style.text} 
                        placeholder="Votre pseudo"
                        placeholderTextColor="grey"
                        onChangeText={(pseudo) => this.setState({ pseudo: pseudo })}
                        value={this.state.pseudo}
                    />
                    <TextInput 
                        style={style.text} 
                        placeholder="Votre mot de passe"
                        placeholderTextColor="grey"
                        secureTextEntry= {true}
                        onChangeText={(mdp) => this.setState({ mdp: mdp })}
                        value={this.state.mdp}
                    />
                </View>
                <View 
                    style={style.submitContainer}
                >
                    <TouchableOpacity 
                        onPress={this.connexion}
                    >
                        <Text style={style.button}> Se connecter</Text>
                    </TouchableOpacity>
                </View>
            </ScrollView>
        )
    }
}
const style = StyleSheet.create({
    container: {
      flex: 1,
      backgroundColor: '#fff',
      alignItems: 'center',
      justifyContent: 'center',
      marginTop: 100
    },
    submitContainer: {
        alignItems: 'center',
        justifyContent: 'center',
    },
    text: {
        width:300,
		backgroundColor:'lightgrey',
		borderRadius: 25,
		paddingVertical:12,
		fontSize:16,
        color:'black',
		textAlign:'center',
		marginVertical: 10
    },
    button: {
        backgroundColor:'red',
        width:200,
		borderRadius: 25,
		marginVertical: 10,
		paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    },

});    