var NotificationSchemaCollection = Backbone.Collection.extend({
    model: NotificationSchema,
    url: '/notification/schemas'
});