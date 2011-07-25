$PWT.Class.create
(
	{
		$namespace:	'Ext.app',
		$name:		'Application'
	}
)
(
	{
		viewport:			null,
		messageContainer:	null,
		logContainer:		null,
		messageInputField:	null,
		messageSendButton:	null,
		init:				function()
		{
			Ext.Direct.addProvider.apply(Ext.Direct,Ext.app.DIRECT_API);
			this.messageContainer=new Ext.form.DisplayField
			(
				{
					title:		'Chat',
					cls:		'x-form-text',
					style:		'overflow:auto;'
				}
			);
			this.logContainer=new Ext.form.DisplayField
			(
				{
					title:		'Log',
					cls:		'x-form-text',
					style:		'overflow:auto;'
				}
			);
			this.messageInputField=new Ext.form.TextField
			(
				{
					name:		'messageInput',
					width:		448,
					listeners:
					{
						specialkey: function(el,event)
						{
							if(event.getKey()==event.ENTER)
							{
								this.sendMessage();
							}
						}.bind(this)
					}
				}
			);
			this.messageSendButton=new Ext.Button
			(
				{
					text:		'Send',
					width:		50,
					handler:	this.sendMessage.bind(this)
				}
			);
			this.viewport=new Ext.Viewport
			(
				{
					items:
					[
						{
							xtype:		'tabpanel',
							activeItem:	0,
							width:		500,
							height:		300,
							items:
							[
								{
									layout:	'fit',
									title:	'Chat',
									items:	this.messageContainer
								},
								this.logContainer
							]
						},
						{
							layout:	'hbox',
							width:	500,
							items:	[this.messageInputField,this.messageSendButton]
						}
					]
				}
			);
			
			//Bind Events.
			Ext.Direct.on
			(
				'onUpdateSuccess',
				function(event)
				{
					this.log(String.format('<p><i>{0}</i></p>',event.data));
				}.bind(this)
			);
			Ext.Direct.on
			(
				'onNewMessage',
				function(event)
				{
					var messages=Ext.decode(event.data);
					var len=messages.length;
					if (len)
					{
						for (var i=0; i<len; i++)
						{
							this.message
							(
								messages[i].user_name,
								messages[i].log_message,
								messages[i].log_timestamp
							);
						}
					}
				}.bind(this)
			);
			//Join the chat.
			this.join();
		},
		join: function()
		{
			Ext.app.chat.main.join
			(
				function(result,event)
				{
					if (result.success)
					{
						this.log(String.format('<p><i>Joined Chat as {0}, at {1}</i></p>',result.userName,event.timeformat));
						var len=result.initialMessages.length;
						if (len)
						{
							for (var i=0; i<len; i++)
							{
								this.message
								(
									result.initialMessages[i].user_name,
									result.initialMessages[i].log_message,
									result.initialMessages[i].log_timestamp
								);
							}
						}
						this.message
						(
							'Chat Bot',
							result.userName+' Joined the Chat!',
							event.timeformat
						);
						if (result.changeName)
						{
							Ext.MessageBox.prompt
							(
								'Enter a Display Name',
								'Please enter the display name you would like other users to see when you type a message to them.',
								function(button,name)
								{
									Ext.app.chat.main.setName
									(
										name,
										function(resultB,event)
										{
											if (resultB.success)
											{
												this.log(String.format('<p><i>Changed Display Name to <b>{0}</b>, at {1}</i></p>',resultB.userName,event.timeformat));
												this.message
												(
													'Chat Bot',
													String.format('{0} Changed Display Name to <b>{1}</b>',result.userName,resultB.userName),
													event.timeformat
												);
											}
											else
											{
												this.log(String.format('<p><i>Failed to change Display Name, at {0}</i></p>',event.timeformat));
											}
										}.bind(this)
									)
								}.bind(this)
							)
						}
					}
					else
					{
						alert('Failed to join chat.');
					}
				}.bind(this)
			);
		},
		log: function(string)
		{
//			this.logContainer.append(string);
//			if (this.logContainer.el)this.logContainer.el.scrollTo('top',100000,true);
		},
		message: function(username,message,time)
		{
			this.messageContainer.append(String.format('<p>[{0}]<b>[{1}]</b> {2}</p>',time,username,message));
			if (this.messageContainer.el)this.messageContainer.el.scrollTo('top',100000,true);
		},
		sendMessage: function()
		{
			var value=this.messageInputField.getValue();
			if (!Ext.isEmpty(value))
			{
				Ext.app.chat.main.send(value);
				this.messageInputField.setValue('');
			}
		}
	}
);
Ext.onReady
(
	function()
	{
		window.application=new Ext.app.Application();
	}
);