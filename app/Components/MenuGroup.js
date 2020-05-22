import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, flexDirection, AsyncStorage } from 'react-native';

export default class MenuGroup extends Component {
    constructor(props){
        super(props);
        this.state = {
            idGroup: ""
        }
    }
    loadGroup = async () => {
        let groupId = await AsyncStorage.getItem('idGroup');
        this.setState({idGroup: groupId})
    }

    componentDidMount(){
        this.loadGroup().done()
    }
    render(){
        return (
            <ScrollView>
                <View>
                    <TouchableOpacity 
                        style={style.button} 
                        onPress={ () => this.props.navigation.navigate('CahierCotes') }
                    >
                        <Text style={{fontSize: 20,  color: 'white'}}>Cahier de cotes</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Cours') }
                    >
                        <Text style={{fontSize: 20,  color: 'white'}}>Cours</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Evaluations') }
                    >
                        <Text style={{fontSize: 20,  color: 'white'}}>Evaluations</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Eleves') }
                    >
                        <Text style={{fontSize: 20, color: 'white'}}>Eleves</Text>
                    </TouchableOpacity>
                </View>
            </ScrollView>
        )
    }
}
const style= StyleSheet.create({
    container: {
        alignItems: 'center',
        justifyContent: 'center',
    },
    button: {
        flex : 1,
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 10,
        marginLeft: 10,
        width: '95%',
        backgroundColor:'red',
		borderRadius: 25,
		marginVertical: 10,
		paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    }
});