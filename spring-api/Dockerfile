# ---------- Build stage ----------
FROM eclipse-temurin:27-jdk-alpine AS build
WORKDIR /app
COPY mvnw .
COPY .mvn .mvn
COPY pom.xml .
RUN chmod +x mvnw && ./mvnw dependency:go-offline -B
COPY src src
RUN ./mvnw package -DskipTests

# ---------- Runtime stage ----------
FROM eclipse-temurin:27-jre-alpine
WORKDIR /app
COPY --from=build /app/target/lab3-jpa-0.0.1-SNAPSHOT.jar app.jar
EXPOSE 8080
ENTRYPOINT ["java", "-jar", "/app/app.jar"]
